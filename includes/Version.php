<?php


class Version
{
    private string $lastVersionInDatabase;
    private string $lastNewNameVersion;


    /**
     * @param Config_inc $db database
     * @param string $table_name
     * @param string $column_name
     */

    public function setLastVersionFromDatabase(Config_inc $db, string $table_name, string $column_name)
    {
        try {
            $this->lastVersionInDatabase = $db->max("version", "numberVersion")[0]["max"];

        } catch (PDOException $e) {
            throw $e;
        }

    }

    /**
     * @param $decodeInformation
     * @param bool $want_index
     * @return mixed
     * @uses check_big_version
     */
    public function lastVersionFromServer($decodeInformation, bool $want_index = false)
    {
        //give max version or index it  for check with other
        $max = $decodeInformation[0]->nameVersion;
        $index_max = 0;
        for ($i = 1; $i < count($decodeInformation);
             $i++) {
            if ($max < $decodeInformation[$i]->nameVersion) {
                $max = $decodeInformation[$i]->nameVersion;
                $index_max = $i;
            }
        }
        if ($want_index == true)
            return $index_max;
        return $max;

    }

    /**
     * @param string $url =>for example:'https://www.server.com/api.php'
     * @param int $current_version current version from database
     * @return bool has new version or hasn't
     */

    public function checkVersion(int $current_version, array $array_versions): bool
    {

        //max version from server
        $maxVersion = $this->lastVersionFromServer($array_versions);

        //check current version with the version from server
        if ($current_version < $maxVersion)
            return true;
        else
            return false;
    }

    /**
     * @param string $url
     * @return array
     */
    public function informationFromServer(string $url): array
    {
        // Initialize a CURL session.
        $ch = curl_init();
        // Return Page contents.
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        //grab URL and pass it to the variable.
        curl_setopt($ch, CURLOPT_URL, $url);
        $server_information = curl_exec($ch);

        //create array from json and return
        return json_decode($server_information, true);
    }

    public function setInformationInDatabase(Config_inc $connect, array $information)
    {
        //give information of version
         $version = $information["lastVersion"][0]["nameVersion"];
        $name = $information ["lastVersion"][0]["nameFile"];
        $describe = $information ["lastVersion"][0]["describe"];

        //set in database
        $connect->insert("version", ['numberVersion', '`describe`', 'name'], [$version, $describe, $name]);
        $this->lastNewNameVersion = $name;
    }

    /**
     * @return string
     */
    public function getLastVersionInDatabase(): string
    {
        return $this->lastVersionInDatabase;
    }

    /**
     * @return string
     */
    public function getLastNewNameVersion(): string
    {
        return $this->lastNewNameVersion;
    }

    public function DownloadFileAndSet()
    {
        //download file from server and set
        exec("wget " . Config_inc::getServer() . "/server/uploads/" . $this->lastNewNameVersion);
        exec("unzip -o " . $this->lastNewNameVersion . " -d ..");
        exec("rm -r " . $this->lastNewNameVersion);


    }


}