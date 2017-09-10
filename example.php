<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta http-equiv="X-UA-Compatible" content="IE=9; IE=8; IE=7; IE=EDGE" />
<meta name="viewport" content="width=1024">

<title>Генератор объявлений</title>

<!-- Bootstrap -->
<link href="http://adgen.besaba.com/css/bootstrap.min.css" rel="stylesheet">


<script src="//vk.com/js/api/openapi.js" type="text/javascript" charset="windows-1251"></script>

<?php
function parse_express($string)
{
  $m =array();
  preg_match_all("/{[a-zA-Zа-яА-ЯЁё0-9\-\+!<>^`~_☎✆☏►>=\?,\/.*;\\\@#\$%\(\)\"\':\|\s]+}/u",$string,$m);
  if (empty($m[0][0]))
  {
      return $string;
  }
  $result = $string;
  foreach ($m[0] as $id =>$val)
  {
     $result = str_replace($val, parse_elem($val),$result);
  }
  return parse_express($result);
}
function parse_elem($string)
{
   $string = preg_replace("/[{}]/","",$string);
   $arr = explode("|",$string);
   $res= ($arr[rand(0,count($arr) -1)]);
   return $res;
}

$a = 'Заголовок (например): Ремонт {квартир|офисов|коттеджей} под ключ. Гарантия, {бригада|качество}
{===|* * *|***|# # #|~ ~ ~|- - -|___}
Бригада {высококвалифицированных|опытных|ответственных} {ремонтников|работников|мастеров} {выполнит|произведет} {качественный|профессиональный} ремонт {Вашей квартиры|Вашего дома|Вашего жилища} по {разумной|приемлемой} {цене|стоимости}. {У нас|Мы|Ищите у нас|Почему мы|Обращайтесь к нам}:
{>|~|+|=>} {Закупка стройматериалов по {оптовым|сниженным} {ценам|расценкам}|Выполнение {всех видов|любых} работ в {кратчайшие|минимальные} сроки}.
{>|~|+|=>} {{Все|Наши} {рабочие|ремонтники} - граждане РФ|{Опыт|Стаж} {всех|наших} {рабочих|сотрудников|строителей} {5|6|7} лет}.
{>|~|+|=>} Гарантия на {все виды работ|{любые|выполненные} работы} {1|2|3} года.
{>|~|+|=>} {{Все|Любые} виды {ремонтно-отделочных|ремонтных|отделочных} работ {любой сложности|"от A до Я"|под ключ}.|Выезд {специалиста|замерщика} для консультации и замера - БЕСПЛAТНO!}
{===|* * *|***|# # #|~ ~ ~|- - -|___}
{->>|=>|>>|->} ТOЛЬКO до %DATE {действует|работает} СКИДКA на {ремонт|проведение ремонта} {квартир|помещений|Вашей квартиры} «под ключ» – {15|20|10}%!!!
{☎|✆|☏|►} {ЗВОНИТЕ ПО ТЕЛЕФОНУ|ТЕЛЕФОН|ЗВОНИТЕ|Звоните прямо СЕЙЧАС|Звоните и заказывайте БЕСПЛАТНЫЙ замер}: %PHONE';
$s = (isset($_POST['text'])) ? $_POST['text']: $a;
?>
</head>

<body>
<table style="width: 900px; margin: 5px auto; border: 0px;">
	<tbody>
	
		<tr>
			<td style="width:620px; margin:5px 5px 5px 5px; vertical-align:top;">


				<form action="/index.php" method="post">
					<textarea class="form-control" rows="20" cols="140" name="text" text="" placeholder="Введите шаблон, например: {Раскрутка|Продвижение|Оптимизация} Ваших {сайтов|ресурсов|порталов} нашими специалистами."><?php echo $s; ?></textarea>
					<br/>
					<p style="text-align:center;"><button class="btn btn-large btn-primary" type="submit"><span style="font-size:32px;">Хочу Oбъявление!</span></button></p>
				</form>
				<br/>
				<?php
				$m=array();
				$a= preg_match_all("/{(([a-zA-Zа-яА-ЯЁё0-9\s]+)\|?)+}/u",$s,$m);
				$aa= (parse_express($s));
				?>
				<p style="text-align: left;">Еще есть <a href="http://adgen.besaba.com/randomizer" target=_blank><strong>бесплатный рандомизатор текста</strong></a></p>

				<p><strong>P.S.</strong> Используйте в шаблоне ТОЛЬКО конструкции с {текс1|текст2}, как показано в примере на этой странице! Допускается любая вложенность.</p>
				<div class="alert alert-info">
				<h4>Готовое объявление для размещения на avito, irr и других ресурсах:</h4>
				<?php echo nl2br($aa); ?>
				</div>


			</td>
		</tr>

	</tbody>
</table>



</body>
</html>
