<?php

	class Page{
		private $total; //数据表中总记录数
		private $listRows; //每页显示行数
		private $limit;
		private $uri;
		private $pageNum; //页数
		private $config=array('header'=>"个记录", "prev"=>"上一页", "next"=>"下一页", "first"=>"首 页", "last"=>"尾 页");
		private $listNum=8;
		private $pix = '&nbsp';
		/*
		 * $total 
		 * $listRows
		 */
		public function __construct($total, $listRows=10, $pa=""){
			$this->total=$total;
			$this->listRows=$listRows;
			$this->uri=$this->getUri($pa);
			$this->page=!empty($_GET["page"]) ? $_GET["page"] : 1;
			$this->pageNum=ceil($this->total/$this->listRows);
			$this->limit=$this->setLimit();
		}

		private function setLimit(){
			return "Limit ".($this->page-1)*$this->listRows.", {$this->listRows}";
		}

		private function getUri($pa){
			$info = $this->getUrlArray($_SERVER['PATH_INFO']);
			$offset = array_search('page',$info);
			if($offset){
				unset($info[$offset]);
				unset($info[$offset+1]);
			}
			
			$path = implode('/',$info);
			
			$uri = $this->getRootLocation().$path;
			
			return $uri;
		}
		
		
		public function setPix($value){
			$this->pix  = $value;
		}
		
	    private function getUrlArray($url,$piexl='/'){
			$str = explode($piexl,$url);
			return $str;    
	    }
	    
	    private function getRootLocation(){
    

			$location = $_SERVER['SCRIPT_NAME'];
			
			if(getConfItem('REWRITE_ON')){
				$count = strrpos($_SERVER['SCRIPT_NAME'],'/');
				$location = substr($_SERVER['SCRIPT_NAME'],0,$count);
			}
			
			return $location;
	    }	    

		private function __get($args){
			if($args=="limit")
				return $this->limit;
			else
				return null;
		}

		private function start(){
			if($this->total==0)
				return 0;
			else
				return ($this->page-1)*$this->listRows+1;
		}

		private function end(){
			return min($this->page*$this->listRows,$this->total);
		}

		private function first(){
			if($this->page==1)
				$html.='';
			else
				$html.="{$this->pix} {$this->pix} <a href='{$this->uri}/page/1'>{$this->config["first"]}</a>{$this->pix} {$this->pix} ";

			return $html;
		}

		private function prev(){
			if($this->page==1)
				$html.='';
			else
				$html.="{$this->pix} {$this->pix} <a href='{$this->uri}/page/".($this->page-1)."'>{$this->config["prev"]}</a>{$this->pix} {$this->pix} ";

			return $html;
		}

		private function pageList(){
			$linkPage="";
			
			$inum=floor($this->listNum/2);
		
			for($i=$inum; $i>=1; $i--){
				$page=$this->page-$i;

				if($page<1)
					continue;

				$linkPage.="{$this->pix} <a href='{$this->uri}/page/{$page}'>{$page}</a>{$this->pix} ";

			}
		
			$linkPage.="{$this->pix} {$this->page}{$this->pix} ";
			

			for($i=1; $i<=$inum; $i++){
				$page=$this->page+$i;
				if($page<=$this->pageNum)
					$linkPage.="{$this->pix} <a href='{$this->uri}/page/{$page}'>{$page}</a>{$this->pix} ";
				else
					break;
			}

			return $linkPage;
		}

		private function next(){
			if($this->page==$this->pageNum)
				$html.='';
			else
				$html.="{$this->pix} {$this->pix} <a href='{$this->uri}/page/".($this->page+1)."'>{$this->config["next"]}</a>{$this->pix} {$this->pix} ";

			return $html;
		}

		private function last(){
			if($this->page==$this->pageNum)
				$html.='';
			else
				$html.="{$this->pix} {$this->pix} <a href='{$this->uri}/page/".($this->pageNum)."'>{$this->config["last"]}</a>{$this->pix} {$this->pix} ";

			return $html;
		}

		private function goPage(){
			return '{$this->pix} {$this->pix} <input type="text" onkeydown="javascript:if(event.keyCode==13){var page=(this.value>'.$this->pageNum.')?'.$this->pageNum.':this.value;location=\''.$this->uri.'&page=\'+page+\'\'}" value="'.$this->page.'" style="width:25px"><input type="button" value="GO" onclick="javascript:var page=(this.previousSibling.value>'.$this->pageNum.')?'.$this->pageNum.':this.previousSibling.value;location=\''.$this->uri.'&page=\'+page+\'\'">{$this->pix} {$this->pix} ';
		}
		
		private function goSelect(){
			
			$str = '<select name="select">';
			
			for ($i=1; $i <= $this->pageNum; $i++) { 
				$str .= '<option value="'.$i.'">'.$i.'</option>';
			}
			$str .= '</select>';
			
			$str .= ' <a href="'.$this->uri.'/page/$(select)/">跳转</a>';
			return $str;
		}
		
		function show($display=array(0,1,2,3,4,5,6,7,8)){
			$html[0]="{$this->pix} {$this->pix} 共有<b>{$this->total}</b>{$this->config["header"]}{$this->pix} {$this->pix} ";
			$html[1]="{$this->pix} {$this->pix} 每页显示<b>".($this->end()-$this->start()+1)."</b>条，本页<b>{$this->start()}-{$this->end()}</b>条{$this->pix} {$this->pix} ";
			$html[2]="{$this->pix} {$this->pix} <b>{$this->page}/{$this->pageNum}</b>页{$this->pix} {$this->pix} ";
			
			$html[3]=$this->first();
			$html[4]=$this->prev();
			$html[5]=$this->pageList();
			$html[6]=$this->next();
			$html[7]=$this->last();
			//$html[8]=$this->goPage();
			$html[8] = $this->goSelect();
			$fpage='';
			foreach($display as $index){
				$fpage.=$html[$index];
			}

			return $fpage;

		}

	
	}
