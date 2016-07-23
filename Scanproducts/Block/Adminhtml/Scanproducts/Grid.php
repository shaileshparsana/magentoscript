<?php

class Plumtree_Scanproducts_Block_Adminhtml_Scanproducts_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
  public function __construct()
  {
      parent::__construct();
      $this->setId('scanproductsGrid');
      $this->setDefaultSort('scanproducts_id');
      $this->setDefaultDir('ASC');
      $this->setSaveParametersInSession(true);
  }

  protected function _prepareCollection()
  {
      $collection = Mage::getModel('scanproducts/scanproducts')->getCollection();
      $this->setCollection($collection);
      return parent::_prepareCollection();
  }

  protected function _prepareColumns()
  {
      $this->addColumn('scanproducts_id', array(
          'header'    => Mage::helper('scanproducts')->__('ID'),
          'align'     =>'right',
          'width'     => '50px',
          'index'     => 'scanproducts_id',
      ));

      $this->addColumn('filename', array(
          'header'    => Mage::helper('scanproducts')->__('File Name'),
          'align'     =>'left',
          'index'     => 'filename',
      ));
	  $this->addColumn('created_time', array(
          'header'    => Mage::helper('scanproducts')->__('Created Time'),
          'align'     =>'left',
          'index'     => 'created_time',
		  'type'      => 'datetime',
		  'time' => true,
		  'format' =>	'dd MMM, Y, hh:mm:ss a'
		
      ));
		$this->addColumn('customer_id', array(
          'header'    => Mage::helper('scanproducts')->__('Customer Id'),
          'align'     =>'left',
          'index'     => 'customer_id',
      ));
	  /*
      $this->addColumn('content', array(
			'header'    => Mage::helper('scanproducts')->__('Item Content'),
			'width'     => '150px',
			'index'     => 'content',
      ));
	  */

     
	  
        $this->addColumn('action',
            array(
                'header'    =>  Mage::helper('scanproducts')->__('Action'),
                'width'     => '100',
                'type'      => 'action',
                'getter'    => 'getId',
                'actions'   => array(
                    array(
                        'caption'   => Mage::helper('scanproducts')->__('Edit'),
                        'url'       => array('base'=> '*/*/edit'),
                        'field'     => 'id'
                    )
                ),
                'filter'    => false,
                'sortable'  => false,
                'index'     => 'stores',
                'is_system' => true,
        ));
		
		$this->addExportType('*/*/exportCsv', Mage::helper('scanproducts')->__('CSV'));
		$this->addExportType('*/*/exportXml', Mage::helper('scanproducts')->__('XML'));
	  
      return parent::_prepareColumns();
  }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('scanproducts_id');
        $this->getMassactionBlock()->setFormFieldName('scanproducts');

        $this->getMassactionBlock()->addItem('delete', array(
             'label'    => Mage::helper('scanproducts')->__('Delete'),
             'url'      => $this->getUrl('*/*/massDelete'),
             'confirm'  => Mage::helper('scanproducts')->__('Are you sure?')
        ));

        $statuses = Mage::getSingleton('scanproducts/status')->getOptionArray();

        array_unshift($statuses, array('label'=>'', 'value'=>''));
        $this->getMassactionBlock()->addItem('status', array(
             'label'=> Mage::helper('scanproducts')->__('Change status'),
             'url'  => $this->getUrl('*/*/massStatus', array('_current'=>true)),
             'additional' => array(
                    'visibility' => array(
                         'name' => 'status',
                         'type' => 'select',
                         'class' => 'required-entry',
                         'label' => Mage::helper('scanproducts')->__('Status'),
                         'values' => $statuses
                     )
             )
        ));
        return $this;
    }

  public function getRowUrl($row)
  {
      return $this->getUrl('*/*/edit', array('id' => $row->getId()));
  }

}