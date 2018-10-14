<?php
	header('Content-type: text/html; charset=utf-8');
	//header('Content-type: text/html; charset=windows-1251');
	require'phpQuery.php';//Подключаем библиотеку
	
	
	
	/*ф-я преобразования ссылок*/	
	function achref($atr){
		if ($atr{0}<>'h') $atr='https://www.google.com'.$atr;
		return $atr;
	}
	
	/*ф-я получения общего контента*/
	function get_content($url){
		$ch=curl_init($url);
		//curl_setopt($ch,CURLOPT_FOLLOWLOCATION,true);
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
		//curl_setopt($ch,CURLOPT_HEADER,true);
		//curl_setopt($ch,CURLOPT_NOBODY,true);//вывод только заголовков отв.серв. без контента
		$res=curl_exec($ch);
		curl_close($ch);
		return $res;
	};
	
	/*ф-я выбора конкретных данных*/
	function parser($url,$start,$end,&$gr,&$gs){
		static $count=0;//счетчик индексов массивов выходных данных 
		if($start < $end){
			$file=get_content($url);
			$doc=phpQuery::newDocument($file);
			foreach($doc->find('.g') as $article ){
					$article=pq($article);
					foreach($article->find('h3.r') as $h3r){
						$h3r=pq($h3r);
						$a=$h3r->find('a')->attr('href');
						$a=achref($a);
						$h3r->find('a')->attr('href',$a);
						$gr[$count]=$h3r;
						if($h3r->parent()->find('.s')){$gs[$count]=$h3r->parent()->find('.s');
							}else{ $gs[$count]='';};
						$count++;
					};
			};
			
			$next=$doc->find('span.csb')->parent()->next()->find('a.fl');
			
			if(!empty($next)){
				$start++;
				$a=$next->attr('href');
				$url=achref($a);
				parser($url,$start,$end,$gr,$gs);
			};
		};
	};
	
		
	/***********запуск парсера**********/
	
	$gr=[];//объявляем массивы для вывода данных
	$gs=[];
	
	$url='https://www.google.com/search?q=%D1%80%D0%BE%D1%81%D1%82%D0%B5%D0%BB%D0%B5%D0%BA%D0%BE%D0%BC&ei=CkqrW_aPEMnMrgTl1KTIAg&start=80&sa=N&biw=1366&bih=657';//тестовый $url
	
	$start=0;//счетчик страниц
	$end=3;//количество перебираемых страниц
	parser($url,$start,$end,$gr,$gs);
	
	/*Вывод данных*/
	foreach($gr as $key=>$h){
				echo $key;
				echo '<br>';
				echo $h;
				echo '<br>';
				echo $gs[$key];
				echo '<hr>';
				};	
					
?>