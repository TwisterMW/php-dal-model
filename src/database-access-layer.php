<?php
	include_once("db-constants.php");

	class DAL{
		private $dbo;
		private $query;
		private $errorMsg;
		private $outputable;
		private $errorStatus;

		function __construct(){
			$this->query = null;
			$this->outputable = false;
			$this->errorStatus = false;
			$conn = mysqli_connect(DB_SERVER, DB_USER, DB_PASS, DB_NAME);
			
			// Checking the database connection
			if(mysqli_connect_errno()){
				$this->errorTreat("Error: Database connection error! Please, check your database constant declaration.");
			}
		}

		// Initialize database connection
		private function dbConnect(){ $this->dbo = mysqli_connect(DB_SERVER, DB_USER, DB_PASS, DB_NAME); }

		// Closes database connection
		private function dbClose(){ $this->dbo->close(); }

		// Error flag setter (Error code | Error message)
		private function errorTreat($strError){
			$this->errorStatus = true;
			$this->errorMsg = $strError;
		}

		// Outputs the error if any
		private function errorThrow(){
			echo "Error status: " . $this->errorStatus . "<br />";
			echo "Error message: " . $this->errorMsg . "<br /><br />";
		}

		// Executes the query passed by arg
		public function execute($query){
			if($query != null){
				$this->dbConnect();

				$output = array();
				$res = $this->dbo->query($query);

				if(!$res){
					$this->dbClose();
					$this->errorTreat("Error: Fail on query execution. Query: " . $query);
				}else{
					// Checking if the result needs to be outputed (Only Read Case)
					if($this->outputable){
						while($r = $res->fetch_assoc()){array_push($output, $r); }

						$this->dbClose();
						return $output;
					}else{
						$this->dbClose();
						return true;
					}
				}
			}else{
				$this->errorTreat("Error: There is no query to execute");
			}

			if($this->errorStatus) $this->errorThrow();
		}

		// SQL Insert Statement
		public function create($targetTable, $targetFields, $targetValues){
			// Checking if any errors before
			if(!$this->errorStatus){
				if(($targetTable != "") && ($targetFields != "") && ($targetValues != "")){

					$this->outputable = false;
					$size = sizeof($targetFields) == sizeof($targetValues);
					$query = "INSERT INTO " . $targetTable . " (";

					// Checking if targetFields and targetValues are arrays and same-sized
					if((is_array($targetFields)) && (is_array($targetValues)) && ($size)){

						for($i = 0;$i < sizeof($targetFields);$i++){
							$query .= $targetFields[$i];

							if(($i + 1) < sizeof($targetValues)){ $query .= ", "; }
						}

						$query .= ") VALUES (";

						for($i = 0;$i < sizeof($targetValues);$i++){

							// Checking type of targetValues for quotes adding
							switch(gettype($targetValues[$i])){
								case "integer": $query .= $targetValues[$i]; break;
								case "string": $query .= "'" . $targetValues[$i] . "'"; break;
							}

							if(($i + 1) < sizeof($targetValues)){ $query .= ", "; }
						}

						$query .= ")";
					}else{
						// Cheking that targetFields and targetValues are not arrays
						if(!is_array($targetFields) && !is_array($targetValues)){
							$query .= $targetFields . ") VALUES (";
							
							// Checking type of targetValues for quotes adding
							switch(gettype($targetValues)){
								case "integer": $query .= $targetValues; break;
								case "string": $query .= "'" . $targetValues . "'"; break;
							}

							$query .= ")";
						}else{
							$this->errorTreat("Error: 'targetFields' and 'targetValues' are different type of values!");
						}
					}

					// Returning generated query if there are not errors
					if(!$this->errorStatus){
						$this->query = $query;
						return $this->query;
					}

				}else{
					$this->errorTreat("Error: 'targetTable', 'targetFields' or 'targetValues' are null or empty!");
				}

				if($this->errorStatus) $this->errorThrow();
			}else{ $this->errorThrow(); }
		}

		// SQL Select Statement
		public function read($targetTable, $targetFields, $conditionTargetField = null, $conditionTargetValue = null){
			// Checking if any errors before
			if(!$this->errorStatus){
				if(($targetFields != "") && ($targetTable != "")){

					$this->outputable = true;
					if(is_array($targetFields)){
						$targetFields = implode(", ", $targetFields);
					}

					$query = "SELECT " . $targetFields . " FROM " . $targetTable;

					// Only append WHERE statement if conditionTargetField and conditionTargetValue are setted
					if(($conditionTargetField != null) && ($conditionTargetValue != null )){
						$size = sizeof($conditionTargetField) == sizeof($conditionTargetValue);

						// Checking if conditionTargetField and conditionTargetValue are arrays and same-sized
						if((is_array($conditionTargetField)) && (is_array($conditionTargetValue)) && ($size)){
							$query .= " WHERE ";

							for($i = 0;$i < sizeof($conditionTargetField);$i++){
								$query .= $conditionTargetField[$i] . "=";

								// Checking type of conditionTargetValue for quotes adding
								switch(gettype($conditionTargetValue[$i])){
									case "integer": $query .= $conditionTargetValue[$i]; break;
									case "string": $query .= "'" . $conditionTargetValue[$i] . "'"; break;
								}

								if(($i + 1) < sizeof($conditionTargetField)){ $query .= " AND "; }
							}
						}else{

							// Cheking that conditionTargetField and conditionTargetValue are not arrays
							if(!is_array($conditionTargetField) && !is_array($conditionTargetValue)){
								$query .= " WHERE " . $conditionTargetField . "=";

								// Checking type of conditionTargetValue for quotes adding
								switch(gettype($conditionTargetValue)){
									case "integer": $query .= $conditionTargetValue; break;
									case "string": $query .= "'" . $conditionTargetValue . "'"; break;
								}
							}else{
								$this->errorTreat("Error: 'conditionTargetField' and 'conditionTargetValue' are different type of values!");
							}
						}
					}

					// Returning generated query if there are not errors
					if(!$this->errorStatus){
						$this->query = $query;
						return $this->query;
					}

				}else{
					$this->errorTreat("Error: 'targetFields' or 'targetTable' are null or empty!");
				}

				if($this->errorStatus) $this->errorThrow();
			}else{ $this->errorThrow(); }
		}

		// SQL Update Statement
		public function update($targetTable, $targetFields, $targetValues, $conditionTargetField = null, $conditionTargetValue = null){
			// Checking if any errors before
			if(!$this->errorStatus){
				if(($targetTable != "") && ($targetFields != "") && ($targetValues != "")){
					
					$this->outputable = false;
					$query = "UPDATE " . $targetTable . " SET ";
					$size = sizeof($targetFields) == sizeof($targetValues);
					
					// Checking if targetFields and targetValues are arrays and same-sized
					if((is_array($targetFields)) && (is_array($targetValues)) && ($size)){
						
						for($i = 0;$i < sizeof($targetFields);$i++){
							$query .= $targetFields[$i] . "=";

							// Checking type of targetValues for quotes adding
							switch(gettype($targetValues[$i])){
								case "integer": $query .= $targetValues[$i]; break;
								case "string": $query .= "'" . $targetValues[$i] . "'"; break;
							}

							if(($i + 1) < sizeof($targetValues)){ $query .= ", "; }
						}
					}else{
						// Cheking that targetFields and targetValues are not arrays
						if((!is_array($targetFields)) && (!is_array($targetValues))){
							$query .= $targetFields . "=";

							// Checking type of targetValues for quotes adding
							switch(gettype($targetValues)){
								case "integer": $query .= $targetValues; break;
								case "string": $query .= "'" . $targetValues . "'"; break;
							}
						}else{
							$this->errorTreat("Error: 'targetFields' and 'targetValues' are different type of values!");
						}
					}

					// Only append WHERE statement if conditionTargetField and conditionTargetValue are setted
					if(($conditionTargetField != null) && ($conditionTargetValue != null )){
						$query .= " WHERE ";
						$size = sizeof($conditionTargetField) == sizeof($conditionTargetValue);

						// Checking if conditionTargetField and conditionTargetValue are arrays and same-sized
						if((is_array($conditionTargetField)) && (is_array($conditionTargetValue)) && ($size)){
							
							for($i = 0;$i < sizeof($conditionTargetField);$i++){
								$query .= $conditionTargetField[$i] . "=";

								// Checking type of conditionTargetValue for quotes adding
								switch(gettype($conditionTargetValue[$i])){
									case "integer": $query .= $conditionTargetValue[$i]; break;
									case "string": $query .= "'" . $conditionTargetValue[$i] . "'"; break;
								}

								if(($i + 1) < sizeof($conditionTargetField)){ $query .= " AND "; }
							}
						}else{
							// Cheking that conditionTargetField and conditionTargetValue are not arrays
							if(!is_array($conditionTargetField) && !is_array($conditionTargetValue)){
								$query .= $conditionTargetField . "=";

								// Checking type of conditionTargetValue for quotes adding
								switch(gettype($conditionTargetValue)){
									case "integer": $query .= $conditionTargetValue; break;
									case "string": $query .= "'" . $conditionTargetValue . "'"; break;
								}
							}else{
								$this->errorTreat("Error: 'conditionTargetField' and 'conditionTargetValue' are different type of values!");
							}
						}
					}

					// Returning generated query if there are not errors
					if(!$this->errorStatus){
						$this->query = $query;
						return $this->query;
					}

				}else{
					$this->errorTreat("Error: 'targetTable', 'targetFields' or 'targetValues' are null or empty!");
				}

				if($this->errorStatus) $this->errorThrow();
			}else{ $this->errorThrow(); }
		}

		// SQL Delete Statement
		public function delete($targetTable, $conditionTargetField = null, $conditionTargetValue = null){
			// Checking if any errors before
			if(!$this->errorStatus){
				if($targetTable != ""){
					$this->outputable = false;
					$query = "DELETE FROM " . $targetTable;

					// Only append WHERE statement if conditionTargetField and conditionTargetValue are setted
					if(($conditionTargetField != null) && ($conditionTargetValue != null )){
						$query .= " WHERE ";
						$size = sizeof($conditionTargetField) == sizeof($conditionTargetValue);

						// Checking if conditionTargetField and conditionTargetValue are arrays and same-sized
						if((is_array($conditionTargetField)) && (is_array($conditionTargetValue)) && ($size)){

							for($i = 0;$i < sizeof($conditionTargetField);$i++){
								$query .= $conditionTargetField[$i] . "=";

								// Checking type of conditionTargetValue for quotes adding
								switch(gettype($conditionTargetValue[$i])){
									case "integer": $query .= $conditionTargetValue[$i]; break;
									case "string": $query .= "'" . $conditionTargetValue[$i] . "'"; break;
								}

								if(($i + 1) < sizeof($conditionTargetField)){ $query .= " AND "; }
							}
						}else{
							// Cheking that conditionTargetField and conditionTargetValue are not arrays
							if(!is_array($conditionTargetField) && !is_array($conditionTargetValue)){
								$query .= $conditionTargetField . "=";

								// Checking type of conditionTargetValue for quotes adding
								switch(gettype($conditionTargetValue)){
									case "integer": $query .= $conditionTargetValue; break;
									case "string": $query .= "'" . $conditionTargetValue . "'"; break;
								}
							}else{
								$this->errorTreat("Error: 'conditionTargetField' and 'conditionTargetValue' are different type of values!");
							}
						}
					}

					// Returning generated query if there are not errors
					if(!$this->errorStatus){
						$this->query = $query;
						return $this->query;
					}

				}else{
					$this->errorTreat("Error: 'targetTable' is null or empty!");
				}

				if($this->errorStatus) $this->errorThrow();
			}else{ $this->errorThrow(); }
		}
	}
?>
