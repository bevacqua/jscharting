<?php
include("Includes/DataEngine.php");
               class MyDateTime extends DateTime
{
    public static function createFromFormat($format, $time, $timezone = null)
    {
    	
        $version = explode('.', phpversion());
        if(!$timezone) $timezone = new DateTimeZone(date_default_timezone_get());
        if(((int)$version[0] >= 5 && (int)$version[1] >= 2 && (int)$version[2] > 17)){
            return parent::createFromFormat($format, $time, $timezone);
        }
        return new DateTime(date($format, strtotime($time)), $timezone);
    }
}

                $dategrouping =  htmlspecialchars($_GET["dategrouping"]);
                if($dategrouping==NULL)
                {
                    $dategrouping = 'years';
                }
                $startDate =  htmlspecialchars($_GET["startDate"]);
                if(empty($startDate))
                {
                    $startDate = '2013';
                }
                
                $de = new DataEngine();
                               
             switch (strtolower($dategrouping))
                {
            case 'years':   
                $startDate = new DateTime($startDate . '-1-1');
                $endDate = new DateTime('2014-12-31 23:59:59');  
                $de->addParameter($startDate);
                $de->addParameter($endDate);
                $de->dataFields = 'name=Year,yAxis=TotalOrder';
                $de->sqlStatement = 'SELECT YEAR(OrderDate) AS Year, SUM(Total) AS TotalOrder FROM Orders
                    WHERE OrderDate >=? And OrderDate <=? GROUP BY YEAR(OrderDate)'; 
                 
                break;            
            case 'months':
                $startDate = new DateTime($startDate . '-1-1');                
                $endDate = new DateTime(date_format($startDate,"Y") . '-12-31 23:59:59');
                $de->addParameter($startDate);
                $de->addParameter($endDate);

                $de->dataFields = 'name=Month,yAxis=TotalOrder';
                $de->sqlStatement = 'SELECT YEAR(OrderDate) AS Year, MONTH(OrderDate) AS Month, SUM(Total) AS TotalOrder FROM Orders
                    WHERE OrderDate >=? And OrderDate <=? GROUP BY YEAR(OrderDate), MONTH(OrderDate)
                    ORDER BY YEAR(OrderDate), MONTH(OrderDate);'; 
                $de->dateGrouping = "Year";//This setting shows all the months in the year regardless of having data on that month or not.
               
                break;
            case 'days': 
                $startDate = MyDateTime::createFromFormat('Y-M-d H:i:s', $startDate . ' 00:00:00');             
                $endDate = new DateTime(date_format($startDate,"Y") . '-' . date_format($startDate,"m") .'-' . date_format($startDate,"t"). ' 23:59:59'); 

                $de->addParameter($startDate);
                $de->addParameter($endDate);
                $de->dataFields = 'name=Day,yAxis=TotalOrder';                
                $de->sqlStatement = 'SELECT Day(OrderDate) AS Day, SUM(Total) AS TotalOrder FROM Orders
                    WHERE OrderDate >=? And OrderDate <=? GROUP BY Day(OrderDate) ORDER BY Day(OrderDate);'; 
                
                $de->dateGrouping = "Month";//This setting shows all the days in the month regardless of having data on that day or not.
                break;
            case 'hours':                
                $startDate = new DateTime($startDate);                
                $endDate = new DateTime(date_format($startDate,"Y") . '-' . date_format($startDate,"m") .'-' . date_format($startDate,"d"). ' 23:59:59'); 
                $de->addParameter($startDate);
                $de->addParameter($endDate);

                $de->dataFields = 'name=Hour,yAxis=TotalOrder';
                $de->sqlStatement = 'SELECT EXTRACT(HOUR FROM OrderDate) AS Hour, SUM(Total) AS TotalOrder FROM Orders
                    WHERE OrderDate >=? And OrderDate <=? GROUP BY EXTRACT(HOUR FROM OrderDate) ORDER BY EXTRACT(HOUR FROM OrderDate);'; 
                
                $de->dateGrouping = "Day";//This setting shows all the hours in the day regardless of having data on that hour or not.
            
        }
                
        $series = $de->getSeries();
        $strJSON = '{';
        if ($series) { 

            $strJSON .= '"series": ';        
            $strJSON .= $series;  

        }//series  


            $strJSON .='}';  
            echo $strJSON;
        ?>
