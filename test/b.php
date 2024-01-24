<?
exit;
$date1="2021-11-08";

$result =  week_day($date1);

print "<pre>";
print_r($result);
print "</pre>";

//주별 확인(월 ~ 금)
	function week_day($w=""){
		if($w){
			$time = strtotime($w);
		}else{
			$time = time();
		}
		$week = date("w", $time);

		
		if($week == 0){
			$monthday = date('Y-m-d', strtotime("-6 day", $time));
			$friday = date('Y-m-d', strtotime("-2 day", $time));
			$sunday = date('Y-m-d', $time);
		}else if($week == 1){
			$monthday = date('Y-m-d', $time);
			$friday = date('Y-m-d', strtotime("+4 day", $time));
			$sunday = date('Y-m-d', strtotime("+6 day", $time));
		}else if($week == 2){
			$monthday = date('Y-m-d', strtotime("-1 day", $time));
			$friday = date('Y-m-d', strtotime("+3 day", $time));
			$sunday = date('Y-m-d', strtotime("+5 day", $time));
		}else if($week == 3){
			$monthday = date('Y-m-d', strtotime("-2 day", $time));
			$friday = date('Y-m-d', strtotime("+2 day", $time));
			$sunday = date('Y-m-d', strtotime("+4 day", $time));
		}else if($week == 4){
			$monthday = date('Y-m-d', strtotime("-3 day", $time));
			$friday = date('Y-m-d', strtotime("+1 day", $time));
			$sunday = date('Y-m-d', strtotime("+3 day", $time));

			//echo ">>> ". $week . "=========>> " . $sunday;


		}else if($week == 5){
			$monthday = date('Y-m-d', strtotime("-4 day", $time));
			$friday = date('Y-m-d', $time);
			$sunday = date('Y-m-d', strtotime("+2 day", $time));
		}else if($week == 6){
			$monthday = date('Y-m-d', strtotime("-5 day", $time));
			$friday = date('Y-m-d', strtotime("-1 day", $time));
			$sunday = date('Y-m-d', strtotime("+1 day", $time));
		}

		$info = array();
		$info['month'] = $monthday;
		$info['friday'] = $friday;
		$info['sunday'] = $sunday;
		return $info;
	}

?>