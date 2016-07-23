<?php

class Plumtree_Zipcodes_Block_Adminhtml_Zipcodes_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
  public function __construct()
  {
      parent::__construct();
      $this->setId('zipcodesGrid');
      $this->setDefaultSort('zipcodes_id');
      $this->setDefaultDir('ASC');
      $this->setSaveParametersInSession(true);
  }

  protected function _prepareCollection()
  {
      $collection = Mage::getModel('zipcodes/zipcodes')->getCollection();
      $this->setCollection($collection);
      return parent::_prepareCollection();
  }

  protected function _prepareColumns()
  {
      $this->addColumn('zipcodes_id', array(
          'header'    => Mage::helper('zipcodes')->__('ID'),
          'align'     =>'right',
          'width'     => '50px',
          'index'     => 'zipcodes_id',
      ));

      $this->addColumn('email', array(
          'header'    => Mage::helper('zipcodes')->__('Email'),
          'align'     =>'left',
          'index'     => 'email',
      ));
	  $this->addColumn('postcode', array(
          'header'    => Mage::helper('zipcodes')->__('Postcode'),
          'align'     =>'left',
          'index'     => 'postcode',
      ));

	  /*
      $this->addColumn('content', array(
			'header'    => Mage::helper('zipcodes')->__('Item Content'),
			'width'     => '150px',
			'index'     => 'content',
      ));
	  */

      $this->addColumn('status', array(
          'header'    => Mage::helper('zipcodes')->__('Status'),
          'align'     => 'left',
          'width'     => '80px',
          'index'     => 'status',
          'type'      => 'options',
          'options'   => array(
              1 => 'Pending',
              2 => 'Success',
          ),
      ));
	  
        $this->addColumn('action',
            array(
                'header'    =>  Mage::helper('zipcodes')->__('Action'),
                'width'     => '100',
                'type'      => 'action',
                'getter'    => 'getId',
                'actions'   => array(
                    array(
                        'caption'   => Mage::helper('zipcodes')->__('Edit'),
                        'url'       => array('base'=> '*/*/edit'),
                        'field'     => 'id'
                    )
                ),
                'filter'    => false,
                'sortable'  => false,
                'index'     => 'stores',
                'is_system' => true,
        ));
		
		$this->addExportType('*/*/exportCsv', Mage::helper('zipcodes')->__('CSV'));
		$this->addExportType('*/*/exportXml', Mage::helper('zipcodes')->__('XML'));
	  
      return parent::_prepareColumns();
  }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('zipcodes_id');
        $this->getMassactionBlock()->setFormFieldName('zipcodes');

        $this->getMassactionBlock()->addItem('delete', array(
             'label'    => Mage::helper('zipcodes')->__('Delete'),
             'url'      => $this->getUrl('*/*/massDelete'),
             'confirm'  => Mage::helper('zipcodes')->__('Are you sure?')
        ));

        $statuses = Mage::getSingleton('zipcodes/status')->getOptionArray();

        array_unshift($statuses, array('label'=>'', 'value'=>''));
        $this->getMassactionBlock()->addItem('status', array(
             'label'=> Mage::helper('zipcodes')->__('Change status'),
             'url'  => $this->getUrl('*/*/massStatus', array('_current'=>true)),
             'additional' => array(
                    'visibility' => array(
                         'name' => 'status',
                         'type' => 'select',
                         'class' => 'required-entry',
                         'label' => Mage::helper('zipcodes')->__('Status'),
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