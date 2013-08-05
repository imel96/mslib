<?php

/*
 * copied from  http://svn.corp.local/svn/mms/trunk/library/Gji/Paginator/DoctrineAdapter.php
 * depends on doctrine1 and zend1
 * it's a bit quirky because this needs to work with mssql.
 */
namespace Gji\Paginator;

class DoctrineAdapter implements \Zend_Paginator_Adapter_Interface {
	protected $dql;
	protected $rowCount = null;
	protected $order;

	/*
	 * somehow count() method below doesn't work when there's order,
	 * that's why it's separated from the dql so it can be excluded
	 * in count().
	 */
	function __construct($dql, $order = null)
	{
		$this->dql = $dql;
		$this->order = $order;
	}

	public function getItems($offset, $itemCountPerPage)
	{
		$dql = clone $this->dql;
		$dql->limit($itemCountPerPage)
			->offset($offset);
		if ($this->order)
			$dql->orderBy($this->order);
		return $dql->execute()->toArray(true);
	}

	public function count()
	{
		if ($this->rowCount)
			return $this->rowCount;
		$dql = clone $this->dql;
		$n = $dql->select('count(1) as count')
			->fetchOne();
		$this->rowCount = $n->count;
		return $this->rowCount;
	}
}
