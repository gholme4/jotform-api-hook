<?php

namespace JotformApiHook\Http\Controllers;
use Voyager;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class JotformApiController {

	protected $client;
	protected $apiKey;

	function __construct() {
		$this->apiKey = env('JOTFORM_API_KEY', getenv('JOTFORM_API_KEY'));
		$this->client = new \GuzzleHttp\Client(['base_uri' => 'https://api.jotform.com/']);
	}

	public function index(){
		return $this->forms();
	}

	public function forms(){

		try {
			$response = $this->client->request('GET', 'user/forms', [
			    'query' => ['apikey' => $this->apiKey]
			]);
		}
		catch(\Exception $e)
		{
			report($e);
			return view('jotform_api::error', [ 'content' => $e->getMessage() ]);
		}


		$body = json_decode($response->getBody());
		$forms = $body->content;

	    return view('jotform_api::index', [
	    	'forms' => $forms
	    ]);
	}

	public function form($formId){
	    // Anytime the admin visits the theme page we will check if we
	    // need to add any more themes to the database

		try 
		{
			$response = $this->client->request('GET', 'form/' . $formId, [
			    'query' => ['apikey' => $this->apiKey]
			]);
		}
		catch(\Exception $e)
		{
			report($e);
			return view('jotform_api::error', [ 'content' => $e->getMessage() ]);
		}


		$body = json_decode($response->getBody());
		$form = $body->content;
		

		$offset = 0;

		$allSubmissions = array();
		while (count($allSubmissions) < $form->count)
		{

			$response = $this->client->request('GET', 'form/' . $formId . '/submissions', [
			    'query' => ['apikey' => $this->apiKey, 'offset' => $offset, 'limit' => 500]
			]);

			$body = json_decode($response->getBody());
			$submissions = $body->content;

			foreach($submissions as $s)
			{

				array_push($allSubmissions, $s);
			}

			$offset++;
		}

	   
	    return view('jotform_api::form-submissions', [ 'form' => $form, 'submissions' => $allSubmissions ]);
	}

	public function exportFormSubmissions($formId) {

		try {
			$response = $this->client->request('GET', 'form/' . $formId, [
			    'query' => ['apikey' => $this->apiKey]
			]);
		}
		catch(\Exception $e)
		{
			report($e);
			return view('jotform_api::error', [ 'content' => $e->getMessage() ]);
		}

		$body = json_decode($response->getBody());
		$form = $body->content;

		if (count($form->count) == 0 )
		{
			return view('jotform_api::error', [ 'content' => 'No submissions found']);
		}

		$sheetData = array();
		$offset = 0;

		while (count($sheetData) < $form->count)
		{
			$response = $this->client->request('GET', 'form/' . $formId . '/submissions', [
			    'query' => ['apikey' => $this->apiKey, 'offset' => $offset, 'limit' => 500 ]
			]);

			$body = json_decode($response->getBody());
			$submissions = $body->content;		

			if ($offset == 0)
			{
				$fields = array();
				foreach($submissions[0]->answers as $a) 
				{
		            array_push($fields, $a->text );
				}

				array_push($sheetData, $fields);
			}

			foreach($submissions as $s)
			{
				$answers = array();
				foreach($s->answers as $a) 
				{

					if ( property_exists($a, 'answer') ): 
		                if ( is_array($a->answer) || is_object($a->answer) ) 
		                    array_push($answers, json_encode($a->answer));
		                else
		                    array_push($answers, $a->answer );
		            endif;
				}


				array_push($sheetData, $answers);
			}

			$offset++;
		}

		$spreadsheet = new Spreadsheet();
		$sheet = $spreadsheet->getActiveSheet();
		
		$spreadsheet->getActiveSheet()
		    ->fromArray(
		        $sheetData,   // The data to set
		        NULL,        // Array values with this value will not be set
		        'A1'         // Top left coordinate of the worksheet range where
		                     //    we want to set these values (default is A1)
		    );

		$writer = new Xlsx($spreadsheet);

		$pathToFile = storage_path("app/" . $form->title . "-export-" . time() . ".xlsx");

		$writer->save($pathToFile);

		return response()->download($pathToFile)->deleteFileAfterSend(true);

	}

	public function submission($submissionId){
	    // Anytime the admin visits the theme page we will check if we
	    // need to add any more themes to the database
		try
		{
			$response = $this->client->request('GET', 'submission/' . $submissionId, [
			    'query' => ['apikey' => $this->apiKey]
			]);
		}
		catch(\Exception $e)
		{
			report($e);
			return view('jotform_api::error', [ 'content' => $e->getMessage() ]);
		}

		$body = json_decode($response->getBody());
		$submission = $body->content;

	    $response = $this->client->request('GET', 'form/' . $submission->form_id, [
		    'query' => ['apikey' => $this->apiKey]
		]);

		$body = json_decode($response->getBody());
		$form = $body->content;


	   
	    return view('jotform_api::submission', [ 'form' => $form, 'submission' => $submission ]);
	}

	
}