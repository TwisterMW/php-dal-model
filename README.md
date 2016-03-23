#PHP-DALModel
Database Access Layer Class for secure PHP-SQL communication

##DAL USAGE
###Initializing / Basic configuration
You need to update the constant values at <i>db-constants.php</i> file with your database right values
```php
define("DB_SERVER", "");
define("DB_USER", "");
define("DB_PASS", "");
define("DB_NAME", "");
```
**IMPORTANT** The database-access-layer.php and db-constants.php files must be at same location.

###Including / Instantiating
```php
include_once("database-access-layer.php");
$db = new DAL();
```

###Creating and executing queries
You can save the generated query and after pass it to **execute** method
```php
$q = $db->create(
	"test-table",
	array("field1", "field2", "field3"),
	array("value1", "value2", "value3")
);

$db->execute($q);
```

Or you can directly execute a query like that:
```php
$db->execute(
	$db->create(
		"test-table",
		array("field1", "field2", "field3"),
		array("value1", "value2", "value3")
	)
);
```

##CRUD Definition
###CREATE Statement
create($targetTable, $targetFields, $targetValues)
```php
print_r(
	$db->create(
		"test-table",
		array("field1", "field2", "field3"),
		array("value1", "value2", "value3")
	)
);
```
**Output:** INSERT INTO test-table (field1, field2, field3) VALUES ('value1', 'value2', 'value3')

###READ Statement
read($targetTable, $targetFields, $conditionTargetField = null, $conditionTargetValue = null)
```php
print_r(
	$db->read(
		"test-table",
		array("field1", "field2"),
		array("fieldToCompare1", "fieldToCompare2", "fieldToCompare3"),
		array("valueToCompare1", "valueToCompare2", "valueToCompare3")
	)
);
```
**Output:** SELECT field1, field2 FROM test-table WHERE fieldToCompare1='valueToCompare1' AND fieldToCompare2='valueToCompare2' AND fieldToCompare3='valueToCompare3'

###UPDATE Statement
update($targetTable, $targetFields, $targetValues, $conditionTargetField = null, $conditionTargetValue = null)
```php
print_r(
	$db->update(
		"test-table",
		array("fieldToUpdate1", "fieldToUpdate2", "fieldToUpdate3"),
		array("valueToUpdate1", "valueToUpdate2", "valueToUpdate3"),
		array("fieldToCompare1", "fieldToCompare2", "fieldToCompare3"),
		array("valueToCompare1", "valueToCompare2", "valueToCompare3")
	)
);
```
**Output:** UPDATE test-table SET fieldToUpdate1='valueToUpdate1', fieldToUpdate2='valueToUpdate2', fieldToUpdate3='valueToUpdate3' WHERE fieldToCompare1='valueToCompare1' AND fieldToCompare2='valueToCompare2' AND fieldToCompare3='valueToCompare3'

###DELETE Statement
delete($targetTable, $conditionTargetField = null, $conditionTargetValue = null)
```php
print_r(
	$db->delete(
		"test-table",
		array("fieldToCompare1", "fieldToCompare2", "fieldToCompare3"),
		array("valueToCompare1", "valueToCompare2", "valueToCompare3")
	)
);
```
**Output:** DELETE FROM test-table WHERE fieldToCompare1='valueToCompare1' AND fieldToCompare2='valueToCompare2' AND fieldToCompare3='valueToCompare3'
