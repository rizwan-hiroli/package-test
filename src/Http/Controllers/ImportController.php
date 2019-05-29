<?php

namespace hiriz\import\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;
use Excel;
use Schema;

class ImportController extends Controller
{
    public function index()
    {
        return view('import::import');
    }

    public function importFile(Request $request)
    {
    	$fileName = $request->file('file');
    	$tableName = $request->table_name;  
    	$columnsName = $request->column_name;
    	$filePath = '';
    	// $columnsName = $this->formatColumnNames($columnsName);
    	$userInputs = array('table_name' => $request->table_name);
		$rules = array('table_name' => 'required');
		$validation = validator::make($userInputs, $rules);
		if ($validation->fails()) {
			return response()->json(['result' => 'failure', 'messages' => 'Please enter table name.'], 200);
		}

		if ($request->hasFile('file')) {
	    	$result = $this->readExcelWithoutValidation($fileName,$filePath);
	    	if($result['result']){
	    		$insertedToDb = $this->insertIntoDb($result,$tableName);	
	    	}else{
	    		$result = ['result'=>'failure','messages'=>$result['message']];
	    		return response()->json($result);
	    	}
	    	if($insertedToDb){
	    		$result = ['result'=>'success'];	
	    		return response()->json($result);
	    	}
	    	else{
	    		$result = ['result'=>'failure'];
	    		return response()->json($result);
	    	}
		}else{
			$result = ['result'=>'failure','messages'=>'Please select file.'];
	    	return response()->json($result);
		}

    	// $sheetColumns = array_keys($result['data'][0]);
    	// $validationResult = $this->validateSheetColumns($tableName,$sheetColumns);
    	
    	
    	
    }

    /**
     * Read excel without validation.
     *
     * @param String $file
     * @param String $filePath
     * @param integer $sheetIndex
     * @return array
     */
    public function readExcelWithoutValidation($file, $filePath, $sheetIndex = 0)
    {
        $res = ['result' => false, 'message' => 'Failed. Invalid Sheet found!.', 'filePath' => null, 'data' => []];
        // validate file.
        $validator = Validator::make(
            [
                'extension' => strtolower($file->getClientOriginalExtension())
            ],
            [
                'extension' => 'required|in:csv',
            ]
        );

        if (!$validator->fails()) {
            // save file
            $fileName = time() . '-' . $file->getClientOriginalName(); //original file name
            $filePath = public_path() . DIRECTORY_SEPARATOR . $filePath;
            $file->move($filePath, $fileName); //save to a path
            $filePath = $filePath . DIRECTORY_SEPARATOR . $fileName; //get the path with filename
            $fileData = [];
            $fileData1 = [];
            $fileData = Excel::selectSheetsByIndex($sheetIndex)->load($filePath, function ($reader) {
                $reader->ignoreEmpty();
            })->get()->toArray();
            $res['result'] = true;
            $res['filePath'] = $filePath;
            $res['data'] = array_filter($fileData);
            $res['message'] = "";
        }
        return $res;
    }

    public function insertIntoDb($data,$tableName)
    {
    	try{
    		\DB::table($tableName)->insert($data['data']);
    		return true;
    	}catch(\Exception $exception){
    		return false;
    	}
    }

    public function formatColumnNames($columns){

    	$columnsName = explode(',', $columns);
    	return $columnsName;

    }

    public function validateSheetColumns($tableName,$sheetColumns)
    {
    	// dd('inside func',$tableName);
    	$tableName = 'import';
    	$databaseColumns = 
    	Schema::getColumnListing($tableName);
    	dd(array_diff($sheetColumns,$databaseColumns));
    	if(true){
    		return false;
    	}
    	return true;

    }

}


