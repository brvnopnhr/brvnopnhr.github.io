<?php
/**
 * @package     Sven.Bluege
 * @subpackage  com_eventgallery
 *
 * @copyright   Copyright (C) 2005 - 2013 Sven Bluege All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
// no direct access
defined('_JEXEC') or die('Restricted access');

require_once JPATH_ROOT.'/components/com_eventgallery/library/common/logger.php';

class TableOrder extends JTable
{

    /** @var object Caches the row data on load for future reference */
    private $_selfCache = null;

    public $id;
    public $documentno;
    public $userid;
    public $email;
    public $phone;
    public $statusid;
    public $subtotal;
    public $subtotalcurrency;
    public $total;
    public $totalcurrency;
    public $surchargeid;
    public $paymentmethodid;
    public $shippingmethodid;
    public $billingaddressid;
    public $shippingaddressid;
    public $message;
    public $modified;
    public $created;

    public $orderstatusid;
    public $paymentstatusid;
    public $shippingstatusid;

    public $surchargetotal;
    public $surchargetotalcurrency;

    public $paymenttotal;
    public $paymenttotalcurrency;

    public $shippingtotal;
    public $shippingtotalcurrency;



    /**
     * Constructor
     * @param JDatabaseDriver $db
     */

	function __construct( &$db ) {
		parent::__construct('#__eventgallery_order', 'id', $db);
        JLog::addLogger(
            array(
                'text_file' => 'com_eventgallery_order.log.php',
                'logger' => 'Eventgalleryformattedtext'
            ),
            JLog::ALL,
            'com_eventgallery_order'
        );

    }

    public function store( $updateNulls=false )
	{
        $this->modified = date("Y-m-d H:i:s");
		//if(!$this->onBeforeStore($updateNulls)) return false;
		$result = parent::store($updateNulls);
		if($result) {
			$result = $this->onAfterStore();
		}
		return $result;
	}

	public function load( $keys=null, $reset=true )
	{
		$result = parent::load($keys, $reset);
		$this->onAfterLoad($result);
		return $result;
	}

	/**
	 * Method to reset class properties to the defaults set in the class
	 * definition. It will ignore the primary key as well as any private class
	 * properties.
	 */
	public function reset()
	{
		parent::reset();
		
		if(!$this->onAfterReset()) return false;
	}


    /**
     * Caches the loaded data so that we can check them for modifications upon
     * saving the row.
     */
    public function onAfterLoad(&$result)
    {
        $this->_selfCache = $result ? clone $this : null;
        JLog::add('loaded order ' . $this->id, JLog::INFO, 'com_eventgallery_order');
        return true;
    }

    /**
     * Resets the cache when the table is reset
     * @return bool
     */
    public function onAfterReset()
    {
        JLog::add('reset order ' . $this->id, JLog::INFO, 'com_eventgallery_order');
        $this->_selfCache = null;
        return true;
    }



    /**
     * Automatically run some actions after a subscription row is saved
     */
    protected function onAfterStore()
    {
        JLog::add('Saved order ' . $this->id, JLog::INFO, 'com_eventgallery_order');
        

        return true;
    }

}
