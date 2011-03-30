<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2010 Oliver Hader <oliver@typo3.org>
*  All rights reserved
*
*  This script is part of the TYPO3 project. The TYPO3 project is
*  free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation; either version 2 of the License, or
*  (at your option) any later version.
*
*  The GNU General Public License can be found at
*  http://www.gnu.org/copyleft/gpl.html.
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/

/**
 * Generic test helpers.
 *
 * @author Oliver Hader <oliver@typo3.org>
 */
abstract class tx_irretutorial_Abstract extends tx_phpunit_database_testcase {
	const TABLE_Pages = 'pages';

	const COMMAND_Localize = 'localize';
	const COMMAND_Delete = 'delete';

	/**
	 * @var string
	 */
	private $path;

	/**
	 * @var t3lib_TCEmain
	 */
	private $tceMainOverride;

	/**
	 * @var integer
	 */
	private $expectedLogEntries = 0;

	/**
	 * @var array
	 */
	private $originalConvVars;

	/**
	 * @var array
	 */
	private $originalTca;

	/**
	 * @var t3lib_beUserAuth
	 */
	private $originalBackendUser;

	/**
	 * @var t3lib_beUserAuth
	 */
	private $backendUser;

	/**
	 * Sets up this test case.
	 *
	 * @return void
	 */
	protected function setUp() {
		$this->expectedLogEntries = 0;

		$this->originalTca = $GLOBALS['TCA'];

		$this->originalBackendUser = clone $GLOBALS['BE_USER'];
		$this->backendUser = $GLOBALS['BE_USER'];

		$this->originalConvVars = $GLOBALS['TYPO3_CONF_VARS'];
		$GLOBALS['TYPO3_CONF_VARS']['SYS']['sqlDebug'] = 1;

		$this->initializeDatabase();
	}

	/**
	 * Tears down this test case.
	 *
	 * @return void
	 */
	protected function tearDown() {
		$this->assertNoLogEntries();

		$this->expectedLogEntries = 0;

		$GLOBALS['TCA'] = $this->originalTca;
		unset($this->originalTca);

		$GLOBALS['TYPO3_CONF_VARS'] = $this->originalConvVars;
		unset($this->originalConvVars);

		$GLOBALS['BE_USER'] = $this->originalBackendUser;
		unset($this->originalBackendUser);
		unset($this->backendUser);

		unset($this->tceMainOverride);
		unset($GLOBALS['T3_VAR']['getUserObj']);

		$this->dropDatabase();
	}

	/**
	 * @return boolean
	 */
	protected function createAndUseDatabase() {
		$hasDatabase = parent::createDatabase();

		if ($hasDatabase === TRUE) {
			$this->useTestDatabase();
		} else {
			$this->fail('No test database available');
		}

		return $hasDatabase;
	}

	/**
	 * Initializes a test database.
	 *
	 * @return resource
	 */
	protected function initializeDatabase() {
		$hasDatabase = $this->createAndUseDatabase();

		if ($hasDatabase) {
			$this->importStdDB();
			$this->importExtensions(array('cms', 'irre_tutorial'));

			$this->importDataSet($this->getPath() . 'fixtures/data_pages.xml');
		}

		return $hasDatabase;
	}

	/**
	 * Gets the path to the test directory.
	 *
	 * @return string
	 */
	protected function getPath() {
		if (!isset($this->path)) {
			$this->path = t3lib_extMgm::extPath('irre_tutorial') . 'tests/';
		}

		return $this->path;
	}

	/**
	 * @return t3lib_beUserAuth
	 */
	protected function getBackendUser() {
		return $this->backendUser;
	}

	/**
	 * Overrides the t3lib_TCEmain instance to be used (could be a mock as well).
	 *
	 * @param t3lib_TCEmain $tceMainOverride
	 * @return void
	 */
	protected function setTceMainOverride(t3lib_TCEmain $tceMainOverride = NULL) {
		$this->tceMainOverride = $tceMainOverride;
	}

	/**
	 * Sets the number of expected log entries.
	 *
	 * @param integer $count
	 * @return void
	 */
	protected function setExpectedLogEntries($count) {
		$count = intval($count);

		if ($count > 0) {
			$this->expectedLogEntries = $count;
		}
	}

	/**
	 * @param string $command
	 * @param mixed $value
	 * @param array $tables Table names with list of ids to be edited
	 * @return array
	 */
	protected function getElementStructureForCommands($command, $value, array $tables) {
		$commandStructure = array();

		foreach ($tables as $tableName => $idList) {
			$ids = t3lib_div::trimExplode(',', $idList, TRUE);
			foreach ($ids as $id) {
				$commandStructure[$tableName][$id] = array(
					$command => $value
				);
			}
		}

		return $commandStructure;
	}

	/**
	 * Simulates executing commands by using t3lib_TCEmain.
	 *
	 * @param  array $elements The cmdmap to be delivered to t3lib_TCEmain
	 * @return t3lib_TCEmain
	 */
	protected function simulateCommandByStructure(array $elements) {
		$tceMain = $this->getTceMain();
		$tceMain->start(array(), $elements);
		$tceMain->process_cmdmap();

		return $tceMain;
	}

	/**
	 * @param string $command
	 * @param mixed $value
	 * @param array $tables Table names with list of ids to be edited
	 * @return t3lib_TCEmain
	 */
	protected function simulateCommand($command, $value, array $tables) {
		return $this->simulateCommandByStructure(
			$this->getElementStructureForCommands($command, $value, $tables)
		);
	}

	/**
	 * Gets the last log entry.
	 *
	 * @return array
	 */
	protected function getLastLogEntryMessage() {
		$message = '';

		$logEntries = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows('*', 'sys_log', 'error IN (1,2)', '', '', 1);

		if (is_array($logEntries) && count($logEntries)) {
			$message = $logEntries[0]['details'];
		}

		return $message;
	}

	/**
	 * @param  array $itemArray
	 * @return array
	 */
	protected function getElementsByItemArray(array $itemArray) {
		$elements = array();

		foreach ($itemArray as $item) {
			$elements[$item['table']][$item['id']] = t3lib_BEfunc::getRecord($item['table'], $item['id']);
		}

		return $elements;
	}

	/**
	 * Gets all records of a table.
	 *
	 * @param string $table Name of the table
	 * @return array
	 */
	protected function getAllRecords($table) {
		return $GLOBALS['TYPO3_DB']->exec_SELECTgetRows('*', $table, '1=1', '', '', '', 'uid');
	}

	/**
	 * Gets the TCE configuration of a field.
	 *
	 * @param  $tableName
	 * @param  $fieldName
	 * @return array
	 */
	protected function getTcaFieldConfiguration($tableName, $fieldName) {
		if (!isset($GLOBALS['TCA'][$tableName]['columns'])) {
			t3lib_div::loadTCA($tableName);
		}

		if (isset($GLOBALS['TCA'][$tableName]['columns'][$fieldName]['config'])) {
			return $GLOBALS['TCA'][$tableName]['columns'][$fieldName]['config'];
		}
	}

	/**
	 * @param string $tableName
	 * @param string $fieldName
	 * @param string $propertyName
	 * @param mixed $value
	 * @return void
	 */
	protected function setTcaFieldConfiguration($tableName, $fieldName, $propertyName, $value) {
		if (!isset($GLOBALS['TCA'][$tableName]['columns'])) {
			t3lib_div::loadTCA($tableName);
		}

		if (isset($GLOBALS['TCA'][$tableName]['columns'][$fieldName]['config'])) {
			$GLOBALS['TCA'][$tableName]['columns'][$fieldName]['config'][$propertyName] = $value;
		}
	}

	/**
	 * @param string $tableName
	 * @param string $fieldName
	 * @param string $behaviourName
	 * @param mixed $value
	 * @return void
	 */
	protected function setTcaFieldConfigurationBehaviour($tableName, $fieldName, $behaviourName, $value) {
		if (!isset($GLOBALS['TCA'][$tableName]['columns'])) {
			t3lib_div::loadTCA($tableName);
		}

		if (isset($GLOBALS['TCA'][$tableName]['columns'][$fieldName]['config'])) {
			if (!isset($GLOBALS['TCA'][$tableName]['columns'][$fieldName]['config']['behaviour'])) {
				$GLOBALS['TCA'][$tableName]['columns'][$fieldName]['config']['behaviour'] = array();
			}

			$GLOBALS['TCA'][$tableName]['columns'][$fieldName]['config']['behaviour'][$behaviourName] = $value;
		}
	}

	/**
	 * Gets the field value of a record.
	 *
	 * @param  $tableName
	 * @param  $id
	 * @param  $fieldName
	 * @return string
	 */
	protected function getFieldValue($tableName, $id, $fieldName) {
		$record = t3lib_BEfunc::getRecord($tableName, $id, $fieldName);
		if (is_array($record)) {
			return $record[$fieldName];
		}
	}

	/**
	 * Gets instance of t3lib_loadDBGroup.
	 *
	 * @return t3lib_loadDBGroup
	 */
	protected function getLoadDbGroup() {
		$loadDbGroup = t3lib_div::makeInstance('t3lib_loadDBGroup');

		return $loadDbGroup;
	}

	/**
	 * Gets an instance of t3lib_TCEmain.
	 *
	 * @return t3lib_TCEmain
	 */
	protected function getTceMain() {
		if (isset($this->tceMainOverride)) {
			$tceMain = $this->tceMainOverride;
		} else {
			$tceMain = t3lib_div::makeInstance('t3lib_TCEmain');
		}

		return $tceMain;
	}

	/**
	 * Assert that no sys_log entries had been written.
	 *
	 * @return void
	 */
	protected function assertNoLogEntries() {
		$logEntries = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows('*', 'sys_log', 'error IN (1,2)');

		if (count($logEntries) > $this->expectedLogEntries) {
			var_dump(array_values($logEntries));
			$this->fail('The sys_log table contains unexpected entries.');
		} elseif (count($logEntries) < $this->expectedLogEntries) {
			$this->fail('Expected count of sys_log entries no reached.');
		}
	}

	/**
	 * Asserts the correct order of elements.
	 *
	 * @param string $table
	 * @param string $field
	 * @param array $expectedOrderOfIds
	 * @param string $message
	 * @return void
	 */
	protected function assertSortingOrder($table, $field, $expectedOrderOfIds, $message) {
		$expectedOrderOfIdsCount = count($expectedOrderOfIds);
		$elements = $this->getAllRecords($table);

		for ($i = 0; $i < $expectedOrderOfIdsCount-1; $i++) {
			$this->assertLessThan(
				$elements[$expectedOrderOfIds[$i+1]][$field],
				$elements[$expectedOrderOfIds[$i]][$field],
				$message
			);
		}
	}

	/**
	 * @param  string $parentTableName
	 * @param  integer $parentId
	 * @param  string $parentFieldName
	 * @param  array $assertions
	 * @param string $mmTable
	 * @return void
	 */
	protected function assertChildren($parentTableName, $parentId, $parentFieldName, array $assertions, $mmTable = '', $expected = TRUE) {
		$tcaFieldConfiguration = $this->getTcaFieldConfiguration($parentTableName, $parentFieldName);

		$loadDbGroup = $this->getLoadDbGroup();
		$loadDbGroup->start(
			$this->getFieldValue($parentTableName, $parentId, $parentFieldName),
			$tcaFieldConfiguration['foreign_table'],
			$mmTable,
			$parentId,
			$parentTableName,
			$tcaFieldConfiguration
		);

		$elements = $this->getElementsByItemArray($loadDbGroup->itemArray);

		foreach ($assertions as $index => $assertion) {
			$this->assertTrue(
				!($expected xor $this->executeAssertionOnElements($assertion, $elements)),
				'Assertion #' . $index . ' failed'
			);
		}
	}

	/**
	 * @param  array $assertion
	 * @param  array $elements
	 * @return boolean
	 */
	protected function executeAssertionOnElements(array $assertion, array $elements) {
		$tableName = $assertion['tableName'];
		unset($assertion['tableName']);

		if (isset($elements[$tableName])) {
			foreach ($elements[$tableName] as $id => $element) {
				$result = FALSE;

				foreach ($assertion as $field => $value) {
					if ($element[$field] == $value) {
						$result = TRUE;
					} else {
						$result = FALSE;
						break;
					}
				}

				if ($result === TRUE) {
					return TRUE;
				}
			}
		}

		return FALSE;
	}
}
