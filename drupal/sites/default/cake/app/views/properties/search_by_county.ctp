<h2><?php echo $this->pageTitle; ?></h2>


<?


echo $form->create('Property', array('action' => 'search', 'admin' => 0, 'type' => 'post')); // form start


echo $form->select('City.name', $city_list, null, array('multiple' => 'checkbox'));
echo $form->input('search_by_county', array('type' => 'hidden', 'value' => 1));

	echo $form->input('submit', array('type' => 'hidden', 'value' => 1));
	echo $form->input('property_subtype_1_display', array('label' => '<b>Property Sub-Types (Residential only)</b>', 'multiple' => 'checkbox', 'options' => $resi_subtypes));

	echo $form->input('transaction_type', array('label' => '<b>Transaction Types</b>', 'multiple' => 'checkbox', 'options' => $transaction_types));
echo $form->end('Search');

/* debug($search_county); */

?>