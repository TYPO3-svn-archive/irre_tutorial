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
abstract class tx_irretutorial_abstractTest extends tx_phpunit_database_testcase {
	const VALUE_TimeStamp = 1250000000;
	const VALUE_WorkspaceId = 9;

	/**
	 * @var boolean
	 */
	private $hasDatabase = FALSE;

	/**
	 * @var string
	 */
	private $path;

	/**
	 * @var integer
	 */
	private $modifiedTimeStamp;

	/**
	 * @var t3lib_beUserAuth
	 */
	private $originalBackendUser;

	/**
	 * Sets up this test case.
	 *
	 * @return void
	 */
	protected function setUp() {
		$this->originalBackendUser = clone $GLOBALS['BE_USER'];
		$GLOBALS['BE_USER']->workspace = self::VALUE_WorkspaceId;
	}

	/**
	 * Tears down this test case.
	 *
	 * @return void
	 */
	protected function tearDown() {
		$GLOBALS['BE_USER'] = $this->originalBackendUser;
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
	 * Gets a modified timestamp to ensure that a record is changed.
	 *
	 * @return integer
	 */
	protected function getModifiedTimeStamp() {
		if (!isset($this->modifiedTimeStamp)) {
			$this->modifiedTimeStamp = self::VALUE_TimeStamp + 100;
		}

		return $this->modifiedTimeStamp;
	}

	/**
	 * Initializes a test database.
	 *
	 * @return resource
	 */
	protected function initializeDatabase() {
		$this->hasDatabase = $this->createDatabase();

		if ($this->hasDatabase === TRUE) {
			$database = $this->useTestDatabase();

			$this->importStdDB();
			$this->importExtensions(array('cms', 'version', 'irre_tutorial'));

			$this->importDataSet($this->getPath() . 'fixtures/data_pages.xml');
			$this->importDataSet($this->getPath() . 'fixtures/data_sys_workspace.xml');

			return $database;
		} else {
			$this->fail('No test database available');
		}
	}

	/**
	 * Purges the test database.
	 *
	 * @return void
	 */
	protected function purgeDatabase() {
		if ($this->hasDatabase === TRUE) {
			$this->dropDatabase();
		}
	}

	/**
	 * Gets an element structure of tables and ids used to simulate editing with TCEmain.
	 *
	 * @param  array $tables Table names with list of ids to be edited
	 * @return array
	 */
	protected function getElementStructureForEditing(array $tables) {
		$editStructure = array();

		foreach ($tables as $tableName => $idList) {
			$ids = t3lib_div::trimExplode(',', $idList, TRUE);
			foreach ($ids as $id) {
				$editStructure[$tableName][$id] = array(
					'tstamp' => $this->getModifiedTimeStamp(),
				);
			}
		}

		return $editStructure;
	}

	/**
	 * @param  array $tables Table names with list of ids to be edited
	 * @return t3lib_TCEmain
	 */
	protected function simulateEditing(array $tables) {
		return $this->simulateEditingByStructure($this->getElementStructureForEditing($tables));
	}

	/**
	 * Simulates editing by using t3lib_TCEmain.
	 *
	 * @param  array $elements The datamap to be delivered to t3lib_TCEmain
	 * @return t3lib_TCEmain
	 */
	protected function simulateEditingByStructure(array $elements) {
		$tceMain = $this->getTceMain();
		$tceMain->start($elements, array());
		$tceMain->process_datamap();

		return $tceMain;
	}

	/**
	 * Asserts that accordant workspace version exist for live versions.
	 *
	 * @param  array $tables Table names with list of ids to be edited
	 * @param  integer $workspaceId Workspace to be used
	 * @return void
	 */
	protected function assertWorkspaceVersions(array $tables, $workspaceId = self::VALUE_WorkspaceId) {
		foreach ($tables as $tableName => $idList) {
			$ids = t3lib_div::trimExplode(',', $idList, TRUE);
			foreach ($ids as $id) {
				$workspaceVersion = t3lib_BEfunc::getWorkspaceVersionOfRecord($workspaceId, $tableName, $id);
				$this->assertTrue(
					$workspaceVersion !== FALSE,
					'No workspace version for ' . $tableName . ':' . $id
				);
			}
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
	protected function assertWorkspaceChildren($parentTableName, $parentId, $parentFieldName, array $assertions, $mmTable = '') {
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
				$this->executeAssertionOnElements($assertion, $elements),
				'Assertion #' . $index . ' failed'
			);
		}
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
	 * @param  array $assertion
	 * @param  array $elements
	 * @return boolean
	 */
	protected function executeAssertionOnElements(array $assertion, array $elements) {
		$tableName = $assertion['tableName'];
		unset($assertion['tableName']);

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

		return FALSE;
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
	 * Gets an instance of t3lib_TCEmain.
	 *
	 * @return t3lib_TCEmain
	 */
	protected function getTceMain() {
		$tceMain = t3lib_div::makeInstance('t3lib_TCEmain');

		return $tceMain;
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

	protected function getWorkpaceVersionId($tableName, $id, $workspaceId = self::VALUE_WorkspaceId) {
		$workspaceVersion = t3lib_BEfunc::getWorkspaceVersionOfRecord($workspaceId, $tableName, $id);
		if ($workspaceVersion !== FALSE) {
			return $workspaceVersion['uid'];
		}
	}
}
