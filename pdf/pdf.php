<?php

//pdf.php

require_once 'class/autoload.inc.php';

use Dompdf\Dompdf;

class Pdf extends Dompdf
{

	public function __construct()
	{
		parent::__construct();
	}
}
