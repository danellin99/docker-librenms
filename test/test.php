<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <title>PHP 萬年曆 - 可選年月</title>
    <style>
        body { font-family: Arial, sans-serif; display: flex; justify-content: center; align-items: center; min-height: 100vh; margin: 0; background-color: #f4f4f9; padding: 20px 0; }
        .calendar { background: #fff; box-shadow: 0 2px 10px rgba(0,0,0,0.1); border-radius: 8px; padding: 20px; width: 450px; }
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
        .header h2 { margin: 0; font-size: 1.2em; color: #333; display: flex; align-items: center; gap: 5px; }
        .header a { text-decoration: none; color: #007bff; font-weight: bold; font-size: 1.2em; padding: 0 10px; }
        select { padding: 5px; border-radius: 4px; border: 1px solid #ddd; font-size: 0.9em; }
        table { width: 100%; border-collapse: collapse; }
        th, td { width: 14.28%; text-align: center; padding: 10px 0; vertical-align: top; }
        th { color: #999; font-weight: normal; border-bottom: 1px solid #eee; }
        td { color: #333; cursor: default; height: 50px; }
        .today { background: #007bff; color: #fff !important; border-radius: 8px; }
        .today .lunar { color: #eee; }
        .lunar { font-size: 0.75em; color: #888; margin-top: 2px; }
        .other-month { color: #ccc; }
    </style>
</head>
<body>

<?php
/**
 * Lunar class 陰曆類庫
 */
class Lunar
{
    public $MIN_YEAR = 1891;
    public $MAX_YEAR = 2100;
    public $lunarInfo = array(
        array(0,2,9,21936),array(6,1,30,9656),array(0,2,17,9584),array(0,2,6,21168),array(5,1,26,43344),array(0,2,13,59728),
        array(0,2,2,27296),array(3,1,22,44368),array(0,2,10,43856),array(8,1,30,19304),array(0,2,19,19168),array(0,2,8,42352),
        array(5,1,29,21096),array(0,2,16,53856),array(0,2,4,55632),array(4,1,25,27304),array(0,2,13,22176),array(0,2,2,39632),
        array(2,1,22,19176),array(0,2,10,19168),array(6,1,30,42200),array(0,2,18,42192),array(0,2,6,53840),array(5,1,26,54568),
        array(0,2,14,46400),array(0,2,3,54944),array(2,1,23,38608),array(0,2,11,38320),array(7,2,1,18872),array(0,2,20,18800),
        array(0,2,8,42160),array(5,1,28,45656),array(0,2,16,27216),array(0,2,5,27968),array(4,1,24,44456),array(0,2,13,11104),
        array(0,2,2,38256),array(2,1,23,18808),array(0,2,10,18800),array(6,1,30,25776),array(0,2,17,54432),array(0,2,6,59984),
        array(5,1,26,27976),array(0,2,14,23248),array(0,2,4,11104),array(3,1,24,37744),array(0,2,11,37600),array(7,1,31,51560),
        array(0,2,19,51536),array(0,2,8,54432),array(6,1,27,55888),array(0,2,15,46416),array(0,2,5,22176),array(4,1,25,43736),
        array(0,2,13,9680),array(0,2,2,37584),array(2,1,22,51544),array(0,2,10,43344),array(7,1,29,46248),array(0,2,17,27808),
        array(0,2,6,46416),array(5,1,27,21928),array(0,2,14,19872),array(0,2,3,42416),array(3,1,24,21176),array(0,2,12,21168),
        array(8,1,31,43344),array(0,2,18,59728),array(0,2,8,27296),array(6,1,28,44368),array(0,2,15,43856),array(0,2,5,19296),
        array(4,1,25,42352),array(0,2,13,42352),array(0,2,2,21088),array(3,1,21,59696),array(0,2,9,55632),array(7,1,30,23208),
        array(0,2,17,22176),array(0,2,6,38608),array(5,1,27,19176),array(0,2,15,19152),array(0,2,3,42192),array(4,1,23,53864),
        array(0,2,11,53840),array(8,1,31,54568),array(0,2,18,46400),array(0,2,7,46752),array(6,1,28,38608),array(0,2,16,38320),
        array(0,2,5,18864),array(4,1,25,42168),array(0,2,13,42160),array(10,2,2,45656),array(0,2,20,27216),array(0,2,9,27968),
        array(6,1,29,44448),array(0,2,17,43872),array(0,2,6,38256),array(5,1,27,18808),array(0,2,15,18800),array(0,2,4,25776),
        array(3,1,23,27216),array(0,2,10,59984),array(8,1,31,27432),array(0,2,19,23232),array(0,2,7,43872),array(5,1,28,37736),
        array(0,2,16,37600),array(0,2,5,51552),array(4,1,24,54440),array(0,2,12,54432),array(0,2,1,55888),array(2,1,22,23208),
        array(0,2,9,22176),array(7,1,29,43736),array(0,2,18,9680),array(0,2,7,37584),array(5,1,26,51544),array(0,2,14,43344),
        array(0,2,3,46240),array(4,1,23,46416),array(0,2,10,44368),array(9,1,31,21928),array(0,2,19,19360),array(0,2,8,42416),
        array(6,1,28,21176),array(0,2,16,21168),array(0,2,5,43312),array(4,1,25,29864),array(0,2,12,27296),array(0,2,1,44368),
        array(2,1,22,19880),array(0,2,10,19296),array(6,1,29,42352),array(0,2,17,42208),array(0,2,6,53856),array(5,1,26,59696),
        array(0,2,13,54576),array(0,2,3,23200),array(3,1,23,27472),array(0,2,11,38608),array(11,1,31,19176),array(0,2,19,19152),
        array(0,2,8,42192),array(6,1,28,53848),array(0,2,15,53840),array(0,2,4,54560),array(5,1,24,55968),array(0,2,12,46496),
        array(0,2,1,22224),array(2,1,22,19160),array(0,2,10,18864),array(7,1,30,42168),array(0,2,17,42160),array(0,2,6,43600),
        array(5,1,26,46376),array(0,2,14,27936),array(0,2,2,44448),array(3,1,23,21936),array(0,2,11,37744),array(8,2,1,18808),
        array(0,2,19,18800),array(0,2,8,25776),array(6,1,28,27216),array(0,2,15,59984),array(0,2,4,27424),array(4,1,24,43872),
        array(0,2,12,43744),array(0,2,2,37600),array(3,1,21,51568),array(0,2,9,51552),array(7,1,29,54440),array(0,2,17,54432),
        array(0,2,5,55888),array(5,1,26,23208),array(0,2,14,22176),array(0,2,3,42704),array(4,1,23,21224),array(0,2,11,21200),
        array(8,1,31,43352),array(0,2,19,43344),array(0,2,7,46240),array(6,1,27,46416),array(0,2,15,44368),array(0,2,5,21920),
        array(4,1,24,42448),array(0,2,12,42416),array(0,2,2,21168),array(3,1,22,43320),array(0,2,9,26928),array(7,1,29,29336),
        array(0,2,17,27296),array(0,2,6,44368),array(5,1,26,19880),array(0,2,14,19296),array(0,2,3,42352),array(4,1,24,21104),
        array(0,2,10,53856),array(8,1,30,59696),array(0,2,18,54560),array(0,2,7,55968),array(6,1,27,27472),array(0,2,15,22224),
        array(0,2,5,19168),array(4,1,25,42216),array(0,2,12,42192),array(0,2,1,53584),array(2,1,21,55592),array(0,2,9,54560)
    );

    function convertSolarToLunar($year, $month, $date) {
        if ($year < $this->MIN_YEAR || $year > $this->MAX_YEAR) return array($year, '', '', '', 0, 0, '', 0);
        $yearData = $this->lunarInfo[$year-$this->MIN_YEAR];
        if($year==$this->MIN_YEAR&&$month <= 2 && $date <= 9){
            return array(1891,'正月','初一','辛卯',1,1,'兔');
        }
        return $this->getLunarByBetween($year, $this->getDaysBetweenSolar($year, $month, $date, $yearData[1], $yearData[2]));
    }

    function getDaysBetweenSolar($year, $cmonth, $cdate, $dmonth, $ddate){
        $a = mktime(0, 0, 0, $cmonth, $cdate, $year);
        $b = mktime(0, 0, 0, $dmonth, $ddate, $year);
        return (int)ceil(($a - $b) / 24 / 3600);
    }

    function getLunarByBetween($year,$between){
        $lunarArray = array();
        if($between == 0){
            array_push($lunarArray, $year, '正月', '初一');
            $t = 1;
            $e = 1;
        }else{
            $year = $between > 0 ? $year : ($year - 1);
            $yearMonth = $this->getLunarYearMonths($year);
            $leapMonth = $this->getLeapMonth($year);
            $between = $between > 0 ? $between : ($this->getLunarYearDays($year) + $between);
            for ($i = 0; $i < 13; $i++){
                if ($between == $yearMonth[$i]) {
                    $t = $i + 2;
                    $e = 1;
                    break;
                } else if ($between<$yearMonth[$i]) {
                    $t=$i+1;
                    $e=$between-(empty($yearMonth[$i-1])?0:$yearMonth[$i-1])+1;
                    break;
                }
            }
            $m = ($leapMonth != 0 && $t == $leapMonth + 1) ? ('閏' . $this->getCapitalNum($t - 1, true)):$this->getCapitalNum(($leapMonth !=0 && $leapMonth + 1 < $t ? ($t - 1) : $t), true);
            $my_year =  $this->toYear($year);
            array_push($lunarArray, $my_year, $m, $this->getCapitalNum($e, false));
        }
        array_push($lunarArray, $this->getLunarYearName($year));
        array_push($lunarArray, $t, $e);
        array_push($lunarArray, $this->getYearZodiac($year));
        array_push($lunarArray, $leapMonth);
        return $lunarArray;
    }

    function toYear($year){
        $arr = array("零", "一", "二", "三", "四", "五", "六", "七", "八", "九");
        $year_str = (string)$year;
        $str = $arr[(int)$year_str[0]].$arr[(int)$year_str[1]].$arr[(int)$year_str[2]].$arr[(int)$year_str[3]];
        return $str;
    }

    function getCapitalNum($num, $isMonth){
        $dateHash = array('0'=>'','1'=>'一','2'=>'二','3'=>'三','4'=>'四','5'=>'五','6'=>'六','7'=>'七','8'=>'八','9'=>'九','10'=>'十 ');
        $monthHash = array('0'=>'','1'=>'正月','2'=>'二月','3'=>'三月','4'=>'四月','5'=>'五月','6'=>'六月','7'=>'七月','8'=>'八月','9'=>'九月','10'=>'十月','11'=>'冬月','12'=>'臘月');
        $res = '';
        if($isMonth){
            $res = $monthHash[$num];
        }else{
            if($num<=10) {
                $res = '初'.$dateHash[$num];
            } else if ($num > 10 && $num < 20){
                $res = '十'.$dateHash[$num - 10];
            } else if ($num==20){
                $res = "二十";
            } else if ($num > 20 && $num < 30){
                $res = "廿".$dateHash[$num - 20];
            } else if ($num == 30){
                $res = "三十";
            }
        }
        return $res;
    }

    function getLunarYearName($year){
        $sky = array('庚','辛','壬','癸','甲','乙','丙','丁','戊','己');
        $earth = array('申','酉','戌','亥','子','丑','寅','卯','辰','巳','午','未');
        $year_str = (string)$year;
        return $sky[(int)$year_str[3]].$earth[$year%12];
    }

    function getYearZodiac($year){
        $zodiac = array('猴','雞','狗','豬','鼠','牛','虎','兔','龍','蛇','馬','羊');
        return $zodiac[$year % 12];
    }

    function getLeapMonth($year){
        $yearData = $this->lunarInfo[$year - $this->MIN_YEAR];
        return $yearData[0];
    }

    function getLunarMonths($year){
        $yearData = $this->lunarInfo[$year - $this->MIN_YEAR];
        $leapMonth = $yearData[0];
        $bit = decbin($yearData[3]);
        $bitArray = array();
        for ($i = 0; $i < strlen($bit);$i ++) {
            $bitArray[$i] = substr($bit, $i, 1);
        }
        for($k=0,$klen=16-count($bitArray);$k<$klen;$k++){
            array_unshift($bitArray, '0');
        }
        $bitArray = array_slice($bitArray, 0, ($leapMonth == 0 ? 12 : 13));
        for($i = 0; $i < count($bitArray); $i++){
            $bitArray[$i] = (int)$bitArray[$i] + 29;
        }
        return $bitArray;
    }

    function getLunarYearMonths($year){
        $monthData = $this->getLunarMonths($year);
        $res=array();
        $temp=0;
        $yearData = $this->lunarInfo[$year - $this->MIN_YEAR];
        $len = ($yearData[0] == 0 ? 12 : 13);
        for($i = 0; $i < $len; $i++){
            $temp=0;
            for($j = 0; $j <= $i; $j++){
                $temp+=$monthData[$j];
            }
            array_push($res, $temp);
        }
        return $res;
    }

    function getLunarYearDays($year){
        $monthArray = $this->getLunarYearMonths($year);
        $len = count($monthArray);
        return $monthArray[$len-1];
    }
}

// 獲取目前的年份和月份
$year = isset($_GET['year']) ? (int)$_GET['year'] : (int)date('Y');
$month = isset($_GET['month']) ? (int)$_GET['month'] : (int)date('n');

// 限制年份範圍 (Lunar 類別支援 1891-2100)
if ($year < 1891) $year = 1891;
if ($year > 2100) $year = 2100;

// 計算上一個月和下一個月
$prevMonth = $month - 1;
$prevYear = $year;
if ($prevMonth < 1) {
    $prevMonth = 12;
    $prevYear--;
}

$nextMonth = $month + 1;
$nextYear = $year;
if ($nextMonth > 12) {
    $nextMonth = 1;
    $nextYear++;
}

// 獲取該月的第一天是星期幾
$firstDayOfMonth = mktime(0, 0, 0, $month, 1, $year);
$daysInMonth = (int)date('t', $firstDayOfMonth);
$dayOfWeek = (int)date('w', $firstDayOfMonth); // 0 (週日) 到 6 (週六)

// 獲取今天的日期
$today = date('Y-n-j');

// 實例化農曆類別
$lunarObj = new Lunar();
?>

<div class="calendar">
    <div class="header">
        <a href="?year=<?php echo $prevYear; ?>&month=<?php echo $prevMonth; ?>">&lt;</a>
        <form method="get" id="dateForm">
            <h2>
                <select name="year" onchange="document.getElementById('dateForm').submit()">
                    <?php for ($y = 1891; $y <= 2100; $y++): ?>
                        <option value="<?php echo $y; ?>" <?php if ($y == $year) echo 'selected'; ?>>
                            <?php echo $y; ?>年
                        </option>
                    <?php endfor; ?>
                </select>
                <select name="month" onchange="document.getElementById('dateForm').submit()">
                    <?php for ($m = 1; $m <= 12; $m++): ?>
                        <option value="<?php echo $m; ?>" <?php if ($m == $month) echo 'selected'; ?>>
                            <?php echo $m; ?>月
                        </option>
                    <?php endfor; ?>
                </select>
            </h2>
        </form>
        <a href="?year=<?php echo $nextYear; ?>&month=<?php echo $nextMonth; ?>">&gt;</a>
    </div>
    <table>
        <thead>
            <tr>
                <th style="color: #e74c3c;">日</th><th>一</th><th>二</th><th>三</th><th>四</th><th>五</th><th style="color: #e74c3c;">六</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <?php
                // 填充第一天之前的空位
                for ($i = 0; $i < $dayOfWeek; $i++) {
                    echo "<td></td>";
                }

                // 填充每一天
                for ($day = 1; $day <= $daysInMonth; $day++) {
                    if (($day + $dayOfWeek - 1) % 7 == 0 && $day != 1) {
                        echo "</tr><tr>";
                    }
                    
                    $currentDate = "$year-$month-$day";
                    $class = ($currentDate == $today) ? 'today' : '';
                    
                    // 獲取農曆資訊
                    $lunarInfo = $lunarObj->convertSolarToLunar($year, $month, $day);
                    $lunarDay = $lunarInfo[2]; // '初一', '初二' 等
                    if ($lunarDay == '初一') {
                        $lunarDay = $lunarInfo[1]; // 顯示月份，如 '正月', '二月'
                    }
                    
                    echo "<td class='$class'>$day<div class='lunar'>$lunarDay</div></td>";
                }

                // 填充最後一週之後的空位
                $remainingDays = (7 - (($daysInMonth + $dayOfWeek) % 7)) % 7;
                for ($i = 0; $i < $remainingDays; $i++) {
                    echo "<td></td>";
                }
                ?>
            </tr>
        </tbody>
    </table>
</div>

</body>
</html>
