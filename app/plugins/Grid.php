<?php
use Phalcon\Events\Event,
	Phalcon\Mvc\User\Plugin,
	Phalcon\Acl,
	Phalcon\Paginator\Adapter\Model;


class Grid extends Plugin
{


	var $columsShow = array();

	public function initialize(){
		$this->Html = '';
		$this->Paginador = '';
		$this->JS = '';
		$this->Total = 0;
		$this->Excel = 0;
		$this->Data = array();
		$this->columsShow = array();
	}

	public function setStatus($check){
		$Status = array(
			"0" => '<span class="label label-xlg arrowed-in-right arrowed-in label-danger">Inactivo</span>',
			"1" => '<span class="label label-paid arrowed-in-right arrowed-in">Activo</span>',
			"3" => "",
			"4" => "",
			"5" => "",
		);
		return $Status[$check];
	}


	public function newGrid($Action){
		//return '<a href="'.$Action.'"><button type="button" class="btn btn-default"><i class="fa fa-plus icon-only"></i></button></a>';
	}

	public function editGrid($Action){
		return '<a href="'.$Action.'" class="btn btn-inverse"><i class="fa fa-pencil icon-only"></i></a>';
	}

	public function deleteGrid($Action){
		return '<a href="'.$Action.'" class="btn btn-danger delete_'.$this->Data["configs"]["name"].'"><i class="fa fa-times icon-only"></i></a>';
	}

	public function detailsGrid($Js="" , $Key=""){
		return '<a target="_blank" href="soporte/detalle/'.$Key.'" class="btn btn-info"><i class="fa fa-external-link icon-only"></i></a>';
	}








	public function excelButton($Action){
		//return '';
		return '<a href="'.$Action.'"><button type="button" class="btn btn-success btn-xs btn-circle"><i class="fa fa-file-excel-o icon-only"></i></button></a>';
		return '<a href="'.$Action.'"><button type="button" class="btn btn-success"><i class="fa fa-file-excel-o icon-only"></i></button></a>';
	}



	public function Start(){
		$Data = $this->Data;
		$Parameters = array();
		$Types = array();
		$Data["configs"]["slug_sort"] = $Data["configs"]["slug"];
		if( isset($Data["configs"]["where"]) && strlen($Data["configs"]["where"]) > 0 ){
			$Where = $Data["configs"]["where"] . " AND (id!=0 ";		
		} else {
			$Where = "(id!=0 ";
		}
		foreach($Data["columns"] as $value){
			if( isset($_REQUEST["key_".$value[0]]) ){
				if( strlen(trim($_REQUEST["key_".$value[0]])) > 0 ){
					$Parameters["key_".$value[0]] = "%".trim($_REQUEST["key_".$value[0]])."%";
					$Types["key_".$value[0]] = $value[3];
					$Where .= "AND " . $value[0] . " LIKE :key_".$value[0].":" ;
					$Data["configs"]["slug"] .= "key_".$value[0]."=".trim($_REQUEST["key_".$value[0]])."&";
					$Data["configs"]["slug_sort"] .= "key_".$value[0]."=".trim($_REQUEST["key_".$value[0]])."&";
				}
			}
		}
		if( isset($_REQUEST["order"]) ){
			$Data["configs"]["slug"] .= "order=".trim($_REQUEST["order"])."&";
		} 
		if( isset($_REQUEST["sort"]) ){
			$Data["configs"]["slug"] .= "sort=".trim($_REQUEST["sort"])."&";
		}
		$Where .= ")";
		$Table = new $Data["configs"]["table"]();
		if( isset($_REQUEST["order"])  ){
			foreach($this->Data["columns"] as $_key => $_value){
				if( $_value[0] == "" ){
					continue;
				}
				$ColumnasOrder[] = $_value[0];
			}
			if( in_array(trim($_REQUEST["order"]) , $ColumnasOrder) ){
				$Order = trim($_REQUEST["order"]);
				if( isset($_REQUEST["sort"]) && trim($_REQUEST["sort"]) == "desc" ){
					$OrderType = 'DESC';
				} else {
					$OrderType = 'ASC';
				}
			}
		} else {
			$Order = 'id';
			$OrderType = 'ASC';
		}
		$titleGroup = "";
		$valueGroup = "";
		if( isset($Data["configs"]["group"]) ){
			$titleGroup = "group";
			$valueGroup = $Data["configs"]["group"];
		}
		$Result = $Table->find(array(
			"columns" => $Data["configs"]["columns"],
		    "conditions" => $Where,
		    "bind" => $Parameters,
		    "bindTypes" => $Types,
		    "order" => $Order." ".$OrderType,
		    $titleGroup => $valueGroup,
		));
		if( !isset($this->Excel) || $this->Excel == 0 ){
	        $Paginator = new Phalcon\Paginator\Adapter\Model(
	            array(
	                "data" => $Result,
	                "limit"=> $Data["configs"]["limit"],
	                "page" => $this->request->getQuery('page', 'int')
	            )
	        );
	        $Items = $Paginator->getPaginate()->items;
	        $Data["configs"]["paginador"]["next"] = (int) $Paginator->getPaginate()->next;
	        $Data["configs"]["paginador"]["first"] = (int) $Paginator->getPaginate()->first;
	        $Data["configs"]["paginador"]["before"] = (int) $Paginator->getPaginate()->before;
	        $Data["configs"]["paginador"]["current"] = (int) $Paginator->getPaginate()->current;
	        $Data["configs"]["paginador"]["last"] = (int) $Paginator->getPaginate()->last;
	        $Data["configs"]["paginador"]["total_pages"] = (int) $Paginator->getPaginate()->total_pages;
	        $Data["configs"]["paginador"]["total_items"] = (int) $Paginator->getPaginate()->total_items;
	        $X = 0;
	        foreach($Items as $value){
	        	foreach($Data["columns"] as $_key => $_value){
	        		if($_value[0] == ""){
	        			$Valor = '<div align="right"><div class="btn-group btn-group-xs">';
	        			if( !isset($Data["configs"]["index"]) ){
	        				$Data["configs"]["index"] = "id";
	        			}
	        			if( $Data["configs"]["edit"] == 1 ){
	        				$Valor .= $this->editGrid($Data["configs"]["controller"] . "/edit/" . $value->__get($Data["configs"]["index"]));
	        			}
	        			if( $Data["configs"]["delete"] == 1 ){
	        				//$Valor .= $this->deleteGrid($Data["configs"]["controller"] . "/delete/" . (int) $value->__get("id"));
	        				$Valor .= $this->deleteGrid("del/".$value->__get($Data["configs"]["index"]));
	        			}
	        			if( isset($Data["configs"]["details"]) ){
	        				$Valor .= $this->detailsGrid($Data["configs"]["details"][0] , $value->__get($Data["configs"]["details"][1]));
	        			}
	        			$Valor .= '</div></div>';
	        		} else if($_value[0] == "status"){
	        			$Valor = $this->setStatus(utf8_decode($value->__get($_value[0])));
	        		} else {
	        			$Valor = utf8_decode($value->__get($_value[0]));
	        		}
	        		$Data["data"][$X][$_value[0]] = $Valor;
	        	}
	        	$X++;
	        }
    	} else if( $this->Excel == 1 ){
	        $X = 0;
	        foreach($Result as $value){
	        	foreach($Data["columns"] as $_key => $_value){
	        		if($_value[0] == ""){
	        			$Valor = '';
	        			unset($Data["columns"][$_key]);
	        		} else if($_value[0] == "status"){
	        			$Valor = utf8_decode($value->__get($_value[0]));
	        		} else {
	        			$Valor = utf8_decode($value->__get($_value[0]));
	        		}
	        		if( $Valor != ""){
	        			$Data["data"][$X][$_value[0]] = $Valor;
	        		}
	        	}
	        	$X++;
	        }
    	}
		$this->Data = $Data;
	}




	public function getGrid(){
		$this->Html = '';
		$this->Paginador = '';
		$this->JS = '';
		$this->Total = $this->Data["configs"]["paginador"]["total_items"];
		if( $this->Data["configs"]["paginador"]["current"] == 0 ){
    		$page = 1;  
		} else {
			$page = $this->Data["configs"]["paginador"]["current"];
		}
		$Input = '<input type="hidden" value="'.$page.'" id="page" name="page">';
		$ColumnasOrder = array();
		if( isset($_REQUEST["order"])  ){
			foreach($this->Data["columns"] as $_key => $_value){
				if( $_value[0] == "" ){
					continue;
				}
				$ColumnasOrder[] = $_value[0];
			}
			if( in_array(trim($_REQUEST["order"]) , $ColumnasOrder) ){
				$Input .= '<input type="hidden" value="'.trim($_REQUEST["order"]).'" id="order" name="order">';
				if( isset($_REQUEST["sort"]) && trim($_REQUEST["sort"]) == "desc" ){
					$Input .= '<input type="hidden" value="desc" id="sort" name="sort">';
				} else {
					$Input .= '<input type="hidden" value="asc" id="sort" name="sort">';
				}
			}
		} else {
			$Input .= '';
		}
		$Form = 'action="'.$this->Data["configs"]["slug"].'" method="GET" onsubmit="return true;" id="form_'.$this->Data["configs"]["name"].'" name="form_'.$this->Data["configs"]["name"].'"';
		$this->Html .= '<form '.$Form.'>'.$Input.'<table class="table table-bordered table-striped table-hover tc-table table-primary footable" style="width:100% !important">';
		
		$this->createHeader();
		$this->createData();
		$this->createPaginador();
		$this->createFooter();
		$this->Html .= '</table></form>';
		$this->Html .= '<script type="text/javascript">'.$this->JS.'</script>';
		$this->Html .= "<style type='text/css' media='screen'>.delete_".$this->Data["configs"]["name"]."{}</style>";
		return $this->Html;
	}


	public function createHeader(){
		$this->Html .= '<thead>';
		$this->Html .= '<tr class="backgroundTHFFF">';
		$this->columsShow[""] = 1;
		foreach($this->Data["columns"] as $key => $value){
			if( $value[0] != "" ){
				if( isset($_REQUEST["key_".$value[0]]) ){
					$Value = trim($_REQUEST["key_".$value[0]]);
				} else {
					$Value = "";
				}
				if( !isset($value[5]) ){
					$value[5] = 1;
					$this->columsShow[trim($value[0])] = 1;
				} else {
					$value[5] = (int) $value[5];
				}
				if( $value[5] == 1 ){
					$this->Html .= '<th style="width:'.trim($value[2]).' !important"><input type="text" class="input" value="'.$Value.'" placeholder="Busqueda..." id="key_'.trim($value[0]).'" name="key_'.trim($value[0]).'"></th>';
					$this->JS .= '
					$("#key_'.trim($value[0]).'").keypress(function (e) {
						if (e.which == 13) {
							$("#form_'.$this->Data["configs"]["name"].' #page").val(1);
							//$("#form_'.$this->Data["configs"]["name"].'").submit();
							ajaxBody($("#form_'.$this->Data["configs"]["name"].'").attr("action") , "POST" , $("#form_'.$this->Data["configs"]["name"].'").serialize());

							return false;
						}
					});
					';
				}
			} else {
				$url = str_replace("?" , "excel/?" , $this->Data["configs"]["slug"]);
				if( $this->Data["configs"]["new"] == 1 ){
					$this->Html .= '<th style="width:'.trim($value[2]).' !important" align="right"><div style="float:right" align="right">'.trim($value[3]).' '.$this->excelButton($url).' </div></th>';
				} else {
					$this->Html .= '<th style="width:'.trim($value[2]).' !important" align="right"><div style="float:right" align="right">'.$this->excelButton($url).'</div></th>';
				}
			}
		}
		$this->Html .= '</tr>';
		$this->Html .= '<tr class="backgroundTH">';
		foreach($this->Data["columns"] as $key => $value){


			if( !isset($this->Data["configs"]["index"]) ){
				$this->Data["configs"]["index"] = "id";
			}
			//echo "<pre>"; print_r($value); echo "</pre>"; exit();
			if( isset($this->columsShow[$value[0]]) ){
				$Url = "";
				if( $value[1] != "" ){
					if( isset($_REQUEST["order"]) && $value[0] == trim($_REQUEST["order"]) ){
						if( isset($_REQUEST["sort"]) && trim($_REQUEST["sort"]) == "desc" ){
							$Order = "footable-visible footable-sortable footable-sorted-desc";
							$Url = $this->Data["configs"]["slug_sort"] . "order=".trim($value[0])."&sort=asc&";
						} else {
							$Order = "footable-visible footable-sortable footable-sorted";
							$Url = $this->Data["configs"]["slug_sort"] . "order=".trim($value[0])."&sort=desc&";
						}
					} else {
						$Order = "footable-visible footable-sortable";
						$Url = $this->Data["configs"]["slug_sort"] . "order=".trim($value[0])."&sort=asc&";
					}
					if( $value[0] != "" ){
						$this->Html .= '<th onclick="window.location=\''.trim($Url).'\'" style="width:'.trim($value[2]).' !important" class="'.trim($Order).'" >'.trim($value[1]).' <span class="footable-sort-indicator"></span></th>';
					} else {
						$this->Html .= '<th style="width:'.trim($value[2]).' !important" >'.trim($value[1]).'</th>';
					}
				} else {
					$this->Html .= '<th style="width:'.trim($value[2]).' !important">&nbsp;</th>';
				}
			}

		}
		$this->Html .= '</tr>';
		$this->Html .= '</thead>';
	}


	public function createData(){
		$this->Html .= '<tbody>';
		foreach($this->Data["data"] as $value){
			if( !isset($this->Data["configs"]["index"]) ){
				$this->Data["configs"]["index"] = "id";
			}
			//echo "<pre>"; print_r($this->columsShow); echo "</pre>"; exit();
			$this->Html .= '<tr id="row_'.$this->Data["configs"]["name"].'_'.$value[$this->Data["configs"]["index"]].'">';
			foreach($value as $key => $_value){
				if( isset($this->columsShow[$key]) ){
					if( isset($this->Data["configs"]["html"]) && in_array($key , $this->Data["configs"]["html"]) ){
						$this->Html .= '<td>'.substr(trim(utf8_encode(strip_tags($_value))) , 0 , 85).'...</td>';
					} else {
						$this->Html .= '<td>'.trim(utf8_encode($_value)).'</td>';
					}
				}
			}
			$this->Html .= '</tr>';
		}
		$this->Html .= '</tbody>';
	}




	public function createPaginador(){
		if( $this->Data["configs"]["paginador"]["current"] == 0 ){
    		$page = 1;  
		} else {
			$page = $this->Data["configs"]["paginador"]["current"];
		}
		$prev = $page - 1;
		$next = $page + 1;
		$lastpage = $this->Data["configs"]["paginador"]["total_pages"];
		$lpm1 = $lastpage - 1;
		$adjacents = 2;
		$url = $this->Data["configs"]["slug"];

	    $pagination = "";
	    if($lastpage > 1)
	    {   
	        $pagination .= "";
	        //previous button
	        if ($page > 1) {
	            //$pagination.= '<a href="'.urlWeb.'search/'.$_REQUEST["q"].'/pagina/'.$prev.'/">< anterior</a>';
	            $pagination .= '<li class="footable-page-arrow"><a data-page="prev" href="'.$url.'page='.$prev.'">‹</a></li>';
	            $paginaAnteriorSeo = $prev;
	        }
	        else {
	            //$pagination.= '<span class="disabled">< anterior</span>';
	            $pagination .= '<li class="footable-page-arrow disabled"><a data-page="prev" href="'.$url.'page='.$page.'">‹</a></li>';
	        }
	        //pages 
	        if ($lastpage < 7 + ($adjacents * 2))
	        {   
	            for ($counter = 1; $counter <= $lastpage; $counter++)
	            {
	                if ($counter == $page)
	                    //$pagination.= '<span class="current">'.$counter.'</span>';
	                    $pagination .= '<li class="footable-page active"><a data-page="0" href="'.$url.'page='.$counter.'">'.$counter.'</a></li>';
	                else
	                    //$pagination.= '<a href="'.urlWeb.'search/'.$_REQUEST["q"].'/pagina/'.$counter.'/">'.$counter.'</a>';
	                	$pagination .= '<li class="footable-page"><a data-page="0" href="'.$url.'page='.$counter.'">'.$counter.'</a></li>';
	            }
	        }
	        elseif($lastpage > 5 + ($adjacents * 2))    //enough pages to hide some
	        {
	            //close to beginning; only hide later pages
	            if($page < 1 + ($adjacents * 2))        
	            {
	                for ($counter = 1; $counter < 4 + ($adjacents * 2); $counter++)
	                {
	                    if ($counter == $page)
	                        //$pagination.= '<span class="current">'.$counter.'</span>';
	                    	$pagination .= '<li class="footable-page active"><a data-page="0" href="'.$url.'page='.$counter.'">'.$counter.'</a></li>';
	                    else
	                        //$pagination.= '<a href="'.urlWeb.'search/'.$_REQUEST["q"].'/pagina/'.$counter.'/">'.$counter.'</a>';
	                    	$pagination .= '<li class="footable-page"><a data-page="0" href="'.$url.'page='.$counter.'">'.$counter.'</a></li>';
	                }
	                
	                $pagination .= '<li class="footable-page disabled"><a data-page="prev" href="'.$url.'page='.$page.'">...</a></li>';
	                $pagination .= '<li class="footable-page"><a data-page="0" href="'.$url.'page='.$lpm1.'">'.$lpm1.'</a></li>';
	                $pagination .= '<li class="footable-page"><a data-page="0" href="'.$url.'page='.$lastpage.'">'.$lastpage.'</a></li>';
	            }
	            //in middle; hide some front and some back
	            elseif($lastpage - ($adjacents * 2) > $page && $page > ($adjacents * 2))
	            {
	                $pagination .= '<li class="footable-page"><a data-page="0" href="'.$url.'page=1">1</a></li>';
	                $pagination .= '<li class="footable-page"><a data-page="0" href="'.$url.'page=2">2</a></li>';
	                $pagination .= '<li class="footable-page disabled"><a data-page="prev" href="'.$url.'page='.$page.'">...</a></li>';
	                for ($counter = $page - $adjacents; $counter <= $page + $adjacents; $counter++)
	                {
	                    if ($counter == $page)
	                        $pagination .= '<li class="footable-page active"><a data-page="0" href="'.$url.'page='.$counter.'">'.$counter.'</a></li>';
	                    else
	                        $pagination .= '<li class="footable-page"><a data-page="0" href="'.$url.'page='.$counter.'">'.$counter.'</a></li>';
	                }
	                $pagination .= '<li class="footable-page disabled"><a data-page="prev" href="'.$url.'page='.$page.'">...</a></li>';
	                $pagination .= '<li class="footable-page"><a data-page="0" href="'.$url.'page='.$lpm1.'">'.$lpm1.'</a></li>';
	                $pagination .= '<li class="footable-page"><a data-page="0" href="'.$url.'page='.$lastpage.'">'.$lastpage.'</a></li>';
	            }
	            //close to end; only hide early pages
	            else
	            {
	                $pagination .= '<li class="footable-page"><a data-page="0" href="'.$url.'page=1">1</a></li>';
	                $pagination .= '<li class="footable-page"><a data-page="0" href="'.$url.'page=2">2</a></li>';
	                $pagination .= '<li class="footable-page disabled"><a data-page="prev" href="'.$url.'page='.$page.'">...</a></li>';
	                for ($counter = $lastpage - (2 + ($adjacents * 2)); $counter <= $lastpage; $counter++)
	                {
	                    if ($counter == $page)
	                        $pagination .= '<li class="footable-page active"><a data-page="0" href="'.$url.'page='.$counter.'">'.$counter.'</a></li>';
	                    else
	                        $pagination .= '<li class="footable-page"><a data-page="0" href="'.$url.'page='.$counter.'">'.$counter.'</a></li>';
	                }
	            }
	        }
	        
	        //next button
	        if ($page < $counter - 1) {
	            //$pagination.= '<a href="'.urlWeb.'search/'.$_REQUEST["q"].'/pagina/'.$next.'/">siguiente ></a>';
	            $pagination .= '<li class="footable-page-arrow"><a data-page="next" href="'.$url.'page='.$next.'">›</a></li>';
	            $paginaSiguienteSeo = $next;
	        }
	        else {
	            $pagination .= '<li class="footable-page-arrow disabled"><a data-page="prev" href="'.$url.'page='.$page.'">›</a></li>';
	        }
	        $pagination.= "";       
	    }
	    $this->Paginador = $pagination;
	}



	public function createFooter(){
		$this->Html .= '<tfoot>';
		$this->Html .= '<tr>';
		$this->Html .= '	<td colspan="'.count($this->Data["columns"]).'">';
		$this->Html .= '		<div class="btn-group btn-group-sm pull-left">Total de registros : '.(int) $this->Total.'</div>';
		$this->Html .= '		<ul class="hide-if-no-paging pagination pagination-centered pull-right">'.$this->Paginador.'</ul>';
		$this->Html .= '		<div class="clearfix"></div>';
		$this->Html .= '	</td>';
		$this->Html .= '</tr>';
		$this->Html .= '</tfoot>';
	}



	public function Dump(){
		//http://www.codedrinks.com/crear-un-reporte-en-excel-con-php-y-mysql/
		$objPHPExcel = new PHPExcel();
		$objPHPExcel->getProperties()->setCreator("Miguel Lomeli")->setTitle($this->Data["configs"]["excel_title"]);
		$objPHPExcel->setActiveSheetIndex(0)->mergeCells('A1:G1')->setCellValue('A1' , $this->Data["configs"]["excel_title"]);
		$L = array("A","B","C","D","E","F","G","H","I","J","K","L","M","N","O","P","Q","R","S","T","U","V","W","X","Y","Z");
		$X = 0;
		foreach($this->Data["columns"] as $key => $value){
			if( $value[0] != "" ){
				$objPHPExcel->setActiveSheetIndex(0)->setCellValue($L[$X].'3' , trim($value[1]));
				$objPHPExcel->getActiveSheet()->getColumnDimension($L[$X])->setWidth((int) $value[4]);
				$X++;
			}
		}
		$x = $X;
		$X = 0;
		$I = 4;
		foreach($this->Data["data"] as $key => $value){
			$X = 0;
			foreach($value as $_key => $_value){
				if( $value[$_key] != "" ){
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue($L[$X].$I , trim(($_value)));
					$X++;
				}
			}
			$I++;
		}
		$estiloTituloReporte = array(
		    'font' => array(
		        'name'      => 'Verdana',
		        'bold'      => true,
		        'italic'    => false,
		        'strike'    => false,
		        'size' 		=> 15,
		        'color'     => array(
		        'rgb' => 'FFFFFF'
		        )
		    ),
		    'fill' => array(
		        'type'  => PHPExcel_Style_Fill::FILL_SOLID,
		        'color' => array(
		        'argb' => '373737')
		    ),
		    'borders' => array(
		        'allborders' => array(
		            'style' => PHPExcel_Style_Border::BORDER_NONE
		        )
		    ),
		    'alignment' => array(
		        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
		        'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
		        'rotation' => 0,
		        'wrap' => TRUE
		    )
		);
		$estiloTituloColumnas = array(
		    'font' => array(
		        'name'  => 'Verdana',
		        'bold'  => true,
		        'size' 	=> 8,
		        'color' => array(
		        	'rgb' => 'FFFFFF'
		        )
		    ),
		    'fill' => array(
		        'type'  => PHPExcel_Style_Fill::FILL_SOLID,
		        'color' => array(
		        'argb' => '373737')
		    ),
		    'borders' => array(
		        'top' => array(
		            'style' => PHPExcel_Style_Border::BORDER_MEDIUM ,
		            'color' => array(
		                'rgb' => 'ffffff'
		            )
		        ),
		        'bottom' => array(
		            'style' => PHPExcel_Style_Border::BORDER_MEDIUM ,
		            'color' => array(
		             'rgb' => 'ffffff'
		            )
		        )
		    ),
		    'alignment' =>  array(
		        'horizontal'=> PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
		        'vertical'  => PHPExcel_Style_Alignment::VERTICAL_CENTER,
		        'wrap'      => TRUE
		    )
		);
		$estiloInformacion = new PHPExcel_Style();
		$estiloInformacion->applyFromArray( array(
		    'font' => array(
		        'name'  => 'Arial',
		        'color' => array(
		            'rgb' => '000000'
		        )
		    ),
		    'fill' => array(
		    'type'  => PHPExcel_Style_Fill::FILL_SOLID,
		    'color' => array(
		            'argb' => 'D3D3D3 ')
		    ),
		    'borders' => array(
		        'left' => array(
		            'style' => PHPExcel_Style_Border::BORDER_THIN ,
		        'color' => array(
		                'rgb' => 'ffffff'
		            )
		        )
		    )
		));
		$objPHPExcel->getActiveSheet()->getStyle('A1:'.$L[($x-1)].'1')->applyFromArray($estiloTituloReporte);
		$objPHPExcel->getActiveSheet()->getStyle('A3:'.$L[($x-1)].'3')->applyFromArray($estiloTituloColumnas);
		$objPHPExcel->getActiveSheet()->setSharedStyle($estiloInformacion, "A4:".$L[($x-1)].($I-1));
		$objPHPExcel->getActiveSheet()->setTitle($this->Data["configs"]["excel_title"]);
		$objPHPExcel->setActiveSheetIndex(0);
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="'.trim($this->Data["configs"]["excel_title"]).' - '.date("Y-m-d H:i:s").'.xlsx"');
		header('Cache-Control: max-age=0');
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
		$objWriter->save('php://output');
		exit();
	}













}