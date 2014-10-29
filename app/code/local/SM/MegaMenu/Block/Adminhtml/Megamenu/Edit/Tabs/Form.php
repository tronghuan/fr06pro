<?php 
/**
* 
*/
class SM_MegaMenu_Block_Adminhtml_Megamenu_Edit_Tabs_Form
	extends Mage_Adminhtml_Block_Widget_Form
{
	protected function _prepareForm()
	{
		if (Mage::registry('megamenu_data')) {
			$data = Mage::registry('megamenu_data')->getData();
		} else {
			$data = array();
		}
		// Zend_Debug::dump($data);die();
		
		$form = new Varien_Data_Form();
		$this->setForm($form);
		$fieldset = $form->addFieldset('megamenu_form', array(
			'legend' => Mage::helper('sm_megamenu')->__('Mega Menu Infomation')
			));
		$fieldset->addField('name', 'text', array(
			'label' => Mage::helper('sm_megamenu')->__('Mega Menu Name'),
			'class' => 'required-entity',
			'required' => true,
			'name' => 'name'
			));
		$fieldset->addField('is_active', 'select', array(
			'label' => Mage::helper('sm_megamenu')->__('Active'),
			'class' => 'required-entity',
			'required' => true,
			'name' => 'is_active',
			'values' => array(
				0 => 'No',
				1 => 'Yes'
				),
			));
		$fieldset->addField('sort_order', 'text', array(
			'label' => Mage::helper('sm_megamenu')->__('Sort order'),
			'class' => 'required-entity  validate-number',
			'required' => true,
			'name' => 'sort_order',
			));
		$type = $fieldset->addField('type', 'select', array(
			'label' => Mage::helper('sm_megamenu')->__('Mega Menu Type'),
			'class' => 'required-entity',
			'required' => true,
			'name' => 'type',
			'values' => array(
				'category' => 'Category',
				'static_block' => 'Static Block'
				)
			));
		$categorySelect = $fieldset->addField('category_select', 'select', array(
			'label' => Mage::helper('sm_megamenu')->__('Select category'),
			'class' => 'required-entity',
			'required' => true,
			'name' => 'category_select',
			'values' => $this->_getCategories()
			));
		$staticBlockSelect = $fieldset->addField('static_block_select', 'select', array(
			'label' => Mage::helper('sm_megamenu')->__('Select Static Block'),
			'class' => 'required-entity',
			'required' => true,
			'name' => 'static_block_select',
			'values' => Mage::getModel('cms/block')->getCollection()->toOptionArray()
			));

		$this->setChild('form_after', $this->getLayout()
			->createBlock('adminhtml/widget_form_element_dependence')
            ->addFieldMap($type->getHtmlId(), $type->getName())
            ->addFieldMap($categorySelect->getHtmlId(), $categorySelect->getName())
            ->addFieldMap($staticBlockSelect->getHtmlId(), $staticBlockSelect->getName())
            ->addFieldDependence(
                $staticBlockSelect->getName(),
                $type->getName(),
                'static_block'
            )
            ->addFieldDependence(
                $categorySelect->getName(),
                $type->getName(),
                'category'
            )
        );

        $form->setValues($data);
		return parent::_prepareForm();
	}

	protected function _getCategories(){
        $level = Mage::getStoreConfig('sm_megamenu/general/level');
        $data = Mage::getModel('catalog/category')->getCollection()->addAttributeToSelect('name');
        $categories = array();
        $tempCate = array();
        foreach($data as $cate){
            $pre = '';
            for($i=1;$i<$cate['level'];$i++){
                $pre .= '__';
            }
            $tempCate[] = array(
                'label'     => $pre.$cate['name'],
                'value'     => $cate['entity_id'],
                'level'     => $cate['level'],
                'parent_id' => $cate['parent_id']
            );
        }
        //get categories level 0
        foreach($tempCate as $tempKey=>$tempValue){
            if($tempValue['level']==0){
                $categories[] = $tempValue;
                unset($tempCate[$tempKey]);
            }
        }
        //get remain categories
        for($i=0;1;$i++){
            foreach($tempCate as $tempKey=>$tempValue){

                if($tempValue['parent_id']==$categories[$i]['value']){
                    array_splice($categories,$i+1,0,array($tempValue));
                    unset($tempCate[$tempKey]);
                }
            }
            if(count($tempCate)<=0) break;
        }

        return $categories;
	}

	
}