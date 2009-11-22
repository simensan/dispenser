<?php

interface Dispenser_Exporter_Interface {
	
	public function load(Dispenser_Builder $builder);
	public function export();
}