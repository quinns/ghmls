<?php
	if(isset($this->params['named']['filter'])){
		echo '/filter:'.$this->params['named']['filter'];
	}
	if(isset($this->params['named']['client'])){
		echo '/client:'.$this->params['named']['client'];
	}