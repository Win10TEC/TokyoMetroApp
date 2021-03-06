<?php

require_once dirname(__FILE__) . '/../model/ApiConnection.php';
require_once dirname(__FILE__) . '/../config/DataConfig.php';

date_default_timezone_set('Asia/Tokyo');

class getTrainTime
{
    private $apiConnection;

    public function __construct(\ApiConnection $apiConnection = null)
    {
        $this->apiConnection = $apiConnection ? $apiConnection : new ApiConnection();
    }

    public function getTrainTimeList($station)
    {
        $weekdays = null;
        $saturdays = null;
        $holidays = null;
        $trainTime = null;
        $dayList = null;

        foreach ($this->apiConnection->getTrainTime($station) as $item) {
            $t = new DateTime($item["dc:date"]);
            $t->setTimeZone(new DateTimeZone('Asia/Tokyo'));
            $datetime = $t->format('Y-m-d H:i');

            $sameAs = $item["owl:sameAs"];
            $sameAs = str_replace('odpt.StationTimetable:TokyoMetro.', '', $sameAs);

            $operator = $item["odpt:operator"];
            $operator = str_replace('odpt.Operator:', '', $operator);

            $station = $item["odpt:station"];
            $station = str_replace('odpt.Station:TokyoMetro.', '', $station);

            $railway = $item["odpt:railway"];
            $railway = str_replace('odpt.Railway:TokyoMetro.', '', $railway);

            $railDirection = $item["odpt:railDirection"];
            $railDirection = str_replace('odpt.RailDirection:TokyoMetro.', '', $railDirection);

            $trainTime[] = array(
                "date" => $datetime,
                "sameAs" => $sameAs,
                "station" => $station,
                "railway" => $railway,
                "operator" => $operator,
                "railDirection" => $railDirection,
            );
        }
        return $trainTime;
    }

    public function timeTablesWeekday($station)
    {
        $trainTimes = null;
        foreach ($this->apiConnection->getTrainTime($station) as $item) {
            $railDirection = $item["odpt:railDirection"];
            $railDirection = str_replace('odpt.RailDirection:TokyoMetro.', '', $railDirection);

            foreach ($item["odpt:weekdays"] as $weekday) {
                $destinationStation = $weekday["odpt:destinationStation"];
                $destinationStation = str_replace('odpt.Station:', '', $destinationStation);

                $trainType = $weekday["odpt:trainType"];
                $trainType = str_replace('odpt.TrainType:TokyoMetro.', '', $trainType);

                $weekdays = array(
                    "departureTime" => $weekday["odpt:departureTime"],
                    "destinationStation" => $destinationStation,
                    "trainType" => $trainType,
                );

                if ($railDirection === "Ogikubo") {
                    if ($weekdays["destinationStation"] === "TokyoMetro.Marunouchi.Ogikubo") {
                        $trainTimes[] = array(
                            "destinationStation" => "荻窪",
                            "departureTime" => $weekdays["departureTime"],
                            "trainType" => $weekdays["trainType"],
                        );
                    } elseif ($weekdays["destinationStation"] === "TokyoMetro.Marunouchi.Shinjuku") {
                        $trainTimes[] = array(
                            "destinationStation" => "新宿",
                            "departureTime" => $weekdays["departureTime"],
                            "trainType" => $weekdays["trainType"],
                        );
                    } elseif ($weekdays["destinationStation"] === "TokyoMetro.MarunouchiBranch.NakanoFujimicho") {
                        $trainTimes[] = array(
                            "destinationStation" => "中野富士見町",
                            "departureTime" => $weekdays["departureTime"],
                            "trainType" => $weekdays["trainType"],
                        );
                    } elseif ($weekdays["destinationStation"] === "TokyoMetro.Marunouchi.NakanoSakaue") {
                        $trainTimes[] = array(
                            "destinationStation" => "中野坂上",
                            "departureTime" => $weekdays["departureTime"],
                            "trainType" => $weekdays["trainType"],
                        );
                    }
                } else {
                    if ($weekdays["destinationStation"] === "TokyoMetro.Marunouchi.Korakuen") {
                        $trainTimes[] = array(
                            "destinationStation" => "後楽園",
                            "departureTime" => $weekdays["departureTime"],
                            "trainType" => $weekdays["trainType"],
                        );
                    } elseif ($weekdays["destinationStation"] === "TokyoMetro.Marunouchi.Myogadani") {
                        $trainTimes[] = array(
                            "destinationStation" => "茗荷谷",
                            "departureTime" => $weekdays["departureTime"],
                            "trainType" => $weekdays["trainType"],
                        );
                    } elseif ($weekdays["destinationStation"] === "TokyoMetro.Marunouchi.Ikebukuro") {
                        $trainTimes[] = array(
                            "destinationStation" => "池袋",
                            "departureTime" => $weekdays["departureTime"],
                            "trainType" => $weekdays["trainType"],
                        );
                    }
                }
            }
        }
        return $trainTimes;
    }

    public function timeTablesSaturday($station)
    {
        foreach ($this->apiConnection->getTrainTime($station) as $item) {
            foreach ($item["odpt:saturdays"] as $saturday) {
                $destinationStation = $saturday["odpt:destinationStation"];
                $destinationStation = str_replace('odpt.Station:', '', $destinationStation);

                $trainType = $saturday["odpt:trainType"];
                $trainType = str_replace('odpt.TrainType:TokyoMetro.', '', $trainType);

                $saturdays[] = array(
                    "departureTime" => $saturday["odpt:departureTime"],
                    "destinationStation" => $destinationStation,
                    "trainType" => $trainType,
                );
            }
        }
    }

    public function timeTablesHoliday($station)
    {
        foreach ($this->apiConnection->getTrainTime($station) as $item) {
            foreach ($item["odpt:holidays"] as $holiday) {
                $destinationStation = $holiday["odpt:destinationStation"];
                $destinationStation = str_replace('odpt.Station:', '', $destinationStation);

                $trainType = $holiday["odpt:trainType"];
                $trainType = str_replace('odpt.TrainType:TokyoMetro.', '', $trainType);

                $holidays[] = array(
                    "departureTime" => $holiday["odpt:departureTime"],
                    "destinationStation" => $destinationStation,
                    "trainType" => $trainType,
                );
            }
        }
    }
}
