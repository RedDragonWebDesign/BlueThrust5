<?php declare(strict_types=1);
use PHPUnit\Framework\TestCase;

// composer exec phpunit tests

$unit_tester = true;
include('src/_functions.php');

final class _functionsTest extends TestCase {
	public function test__sql_array_select_where__1_condition() {
		$sqlTableAsArray = [
			0=> [
				'privilege_id' => '1',
				'rank_id' => '2',
				'console_id' => '3',
			],
			1=> [
				'privilege_id' => '4',
				'rank_id' => '5',
				'console_id' => '6',
			],
		];
		$condition1Field = 'rank_id';
		$condition1Value = '5';
		$expected = [
			[
				'privilege_id' => '4',
				'rank_id' => '5',
				'console_id' => '6',
			],
		];
		$result = sql_array_select_where($sqlTableAsArray, $condition1Field, $condition1Value);
		return $this->assertSame($expected, $result);
	}

	public function test__sql_array_select_where__2_conditions() {
		$sqlTableAsArray = [
			0=> [
				'privilege_id' => '1',
				'rank_id' => '2',
				'console_id' => '3',
			],
			1=> [
				'privilege_id' => '4',
				'rank_id' => '5',
				'console_id' => '6',
			],
			2=> [
				'privilege_id' => '11',
				'rank_id' => '5',
				'console_id' => '12',
			],
		];
		$condition1Field = 'rank_id';
		$condition1Value = '5';
		$condition2Field = 'console_id';
		$condition2Value = '12';
		$expected = [
			[
				'privilege_id' => '11',
				'rank_id' => '5',
				'console_id' => '12',
			],
		];
		$result = sql_array_select_where($sqlTableAsArray, $condition1Field, $condition1Value, $condition2Field, $condition2Value);
		return $this->assertSame($expected, $result);
	}
}