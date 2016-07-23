<?php
 class Plumtree_Customreport_Block_Adminhtml_Customreport_Grid extends Mage_Adminhtml_Block_Report_Grid {
  
    public function __construct() {
        parent::__construct();
        $this->setId('customreportGrid');
        $this->setDefaultSort('created_at');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
        $this->setSubReportSize(false);
    }
  
    protected function _prepareCollection() {
        parent::_prepareCollection();
      
       $collection = $this->getCollection()->initReport('customreport/customreport');
		
		//print_r($collection);exit;
		
		 $this->setCollection($collection);
    
        return $this;
    }
  
    protected function _prepareColumns() {
       
 		$currencyCode = $this->getCurrentCurrencyCode();
     
		
		$this->addColumn('order_items_name', array(
                'header' => Mage::helper('customreport')->__('SKU'),
                'align' => 'left',
                'sortable' => true,
                'index' => 'order_items_name'
        ));
 $this->addColumn('order_increment_id', array(
                'header' => Mage::helper('customreport')->__('Order Number'),
                'align' => 'left',
                'sortable' => true,
                'index' => 'order_increment_id'
        ));
		
		 
        $this->addExportType('*/*/exportCsv', Mage::helper('customreport')->__('CSV'));
        $this->addExportType('*/*/exportXml', Mage::helper('customreport')->__('XML'));
        return parent::_prepareColumns();
    }
  
    public function getRowUrl($row) {
        return false;
    }
  
    public function getReport($from, $to) {
        if ($from == '') {
            $from = $this->getFilter('report_from');
        }
        if ($to == '') {
            $to = $this->getFilter('report_to');
        }
        
        $totalObj = Mage::getModel('reports/totals');
        $totals = $totalObj->countTotals($this, $from, $to);
        $this->setTotals($totals);
        $this->addGrandTotals($totals);
       
        return $this->getCollection()->getReport($from, $to);
    }
}