<?php
	//Required for $_POST calls
	header('Access-Control-Allow-Origin: *');
	
	//Connection info for database
	$hostname = 'localhost';
	$username = 'root'; //Temporarily for testing purposes, create a MySQL user for this later
	$password = 'cossmic'; //same as above
	$database = 'CoSSMunity';
	
	//Null value for use later in code as parameter
	$nullValue = null;

	
	//Connection to the database
	try {
		$dbh = new PDO('mysql:host='.$hostname.';dbname='.$database, $username, $password);
		//Check if parameters have been set and are not empty.
		if (isset($_POST["household_id"]) && !empty($_POST["username"]) && !empty($_POST["email_hash"]) && !empty($_POST["location"])) {
			$household_id = $_POST["household_id"];
			$username = $_POST["username"];
			$email_hash = $_POST["email_hash"];
			$neighbourhood = $_POST["location"];
			
			
			//Check to see if username is available
			$sqlCheckUsernameAvailability = "
				SELECT COUNT(*)
				FROM household
				WHERE username = :username
				LIMIT 1
				";
			$checkUsernameAvailability = $dbh->prepare($sqlCheckUsernameAvailability);
			$checkUsernameAvailability->bindParam(':username', $username, PDO::PARAM_STR);
			$checkUsernameAvailability->execute();

			
			//If username is available start setting up household in database
			if (!($checkUsernameAvailability->fetchColumn())) {
				$today = date("Y-m-d");
				
				
				//Insert household into the database with the information provided
				$sqlInsertUser = "
					INSERT INTO household(household_id, neighbourhood, username, email_hash, joined)
					VALUES(:household_id, :neighbourhood, :username, :email_hash, :joined)
					";
				$insertUser = $dbh->prepare($sqlInsertUser);
				$insertUser->bindParam(':household_id', $household_id, PDO::PARAM_INT);
				$insertUser->bindParam(':neighbourhood', $neighbourhood, PDO::PARAM_STR);
				$insertUser->bindParam(':username', $username, PDO::PARAM_STR);
				$insertUser->bindParam(':email_hash', $email_hash, PDO::PARAM_STR);
				$insertUser->bindParam(':joined', $today, PDO::PARAM_STR);
				/*$insertUser->bindValue(':residents', getIfEmpty($_POST["residents"]), PDO::PARAM_INT);
				$insertUser->bindValue(':house_type', getIfEmpty($_POST["house_type"]), PDO::PARAM_STR);
				$insertUser->bindValue(':size', getIfEmpty($_POST["size"]), PDO::PARAM_INT);
				$insertUser->bindValue(':age', getIfEmpty($_POST["age"]), PDO::PARAM_INT);
				$insertUser->bindValue(':electric_heating', getIfEmpty($_POST["electric_heating"]), PDO::PARAM_BOOL);
				$insertUser->bindValue(':electric_car', getIfEmpty($_POST["electric_car"]), PDO::PARAM_INT);*/
				$insertUser->execute();
				
				
				//Retrieves achievements that exists for use in set up
				$sqlRetrieveAchievementsID = "
					SELECT achievement_id
					FROM achievement
					";
				$retrieveAchievementsID = $dbh->prepare($sqlRetrieveAchievements);
				$retrieveAchievementsID->execute();
				$achievementsID = $retrieveAchievementsID->fetchAll(PDO::FETCH_NUM);

				
				//Sets up the household connection to the different achievements
				$achievement = null;
				$sqlInsertHouseholdAchievements = "
					INSERT INTO household_achievements(household_household_id, achievement_achievement_id, achieved, date_achieved)
					VALUES(:household_household_id, :achievement_achievement_id, :achieved, :date_achieved)
					";
				$insertHouseholdAchievements = $dbh->prepare($sqlInsertHouseholdAchievements);
				$insertHouseholdAchievements->bindParam(':household_household_id', $household_id, PDO::PARAM_INT);
				$insertHouseholdAchievements->bindParam(':achievement_achievement_id', $achievement, PDO::PARAM_INT);
				$insertHouseholdAchievements->bindParam(':achieved', $achieved = 0, PDO::PARAM_BOOL);
				$insertHouseholdAchievements->bindValue(':date_achieved', $nullValue, PDO::PARAM_STR);
				foreach($achievementsID as $value) {
					$achievement = $value;
					$insertHouseholdAchievements->execute();
				}

				
				//Makes it so the user achieves the first achievement which is registering to CoSSMUnity
				$sqlSetFirstAchievement = "
					UPDATE household_achievements
					SET achieved = 1, date_achieved = :date
					WHERE household_household_id = :household_household_id
					AND achievement_achievement_id = 0
					";
				$setFirstAchievement = $dbh->prepare($sqlSetFirstAchievement);
				$setFirstAchievement->bindParam(':date', $today, PDO::PARAM_STR);
				$setFirstAchievement->bindParam(':household_household_id', $household_id, PDO::PARAM_STR);
				$setFirstAchievement->execute();
				
				
				//Retrieves the ranks that exist for use in set up
				$sqlRetrieveRanksID = "
					SELECT rank_id
					FROM rank
					";
				$retrieveRanksID = $dbh->prepare($sqlRetrieveRanksID);
				$retrieveRanksID->execute();
				$ranksID = $retrieveRanksID->fetchAll(PDO::FETCH_NUM);

				
				//Sets up the household connection to the different ranks
				$rank = null;
				$sqlInsertHouseholdRanks = "
					INSERT INTO household_ranks(household_household_id, rank_rank_id, date_obtained)
					VALUES(:household_household_id, :rank_rank_id, :date_obtained)
					";
				$insertHouseholdRanks = $dbh->prepare($sqlInsertHouseholdRanks);
				$insertHouseholdRanks->bindParam(':household_household_id', $household_id, PDO::PARAM_INT);
				$insertHouseholdRanks->bindParam(':rank_rank_id', $rank, PDO::PARAM_INT);
				$insertHouseholdRanks->bindValue(':date_obtained', $nullValue, PDO::PARAM_STR);
				foreach($ranksID as &$value2) {
					$rank = $value2;
					$insertHouseholdRanks->execute();
				}

				
				//Sets it so that the household has achieved the first rank
				$sqlSetFirstRank = "
					UPDATE household_ranks
					SET obtained = 1
					WHERE household_household_id = :household_household_id
					AND rank_rank_id = (SELECT MIN(household_ranks.rank_rank_id) FROM household_ranks)
					";
				$setFirstRank = $dbh->prepare($sqlSetFirstRank);
				$setFirstRank->bindParam(':household_household_id', $household_id, PDO::PARAM_INT);
				$setFirstRank->execute();
				
				
				//Is used to check for score types and insert them into the database.
				$scoreTypeKeys = array("Total Score", "PV Score", "Grid Score", "Scheduling Score", "Share Score");
				$scoreType = array(0,1,2,3,4);
				$scoreTypes = array_combine($scoreTypeKeys, $scoreType);
				
				//Is used as parameters in MySQL and DBO
				$type = null;
				$startOfMonth = date("Y-m")."-01";
				$startDate = null;
				
				//MySQL and DBO for checking if a score exists
				$sqlCheckIfHouseholdScoreExist = "
				SELECT *
				FROM household_scores AS HS
				WHERE HS.household_household_id = :household_id
				AND HS.score_type_score_type_id = :score_type_id
				AND HS.date BETWEEN :startDate AND :endDate
				LIMIT 1
				";
				$checkIfHouseholdScoreExist = $dbh->prepare($sqlCheckIfHouseholdScoreExist);
				$checkIfHouseholdScoreExist->bindParam(":household_id", $household_id, PDO::PARAM_INT);
				$checkIfHouseholdScoreExist->bindParam(":score_type_id", $type, PDO::PARAM_INT);
				$checkIfHouseholdScoreExist->bindParam(":startDate", $startDate, PDO::PARAM_STR);
				$checkIfHouseholdScoreExist->bindParam(":endDate", $today, PDO::PARAM_STR);
				
				//MySQL and DBO for inserting missing household score types
				$sqlInsertHouseholdScoreType = "
				INSERT INTO household_scores(household_household_id, score_type_score_type_id, date, value)
				VALUES (:household_id, :score_type_id, :date, :value)
				";
				$insertHouseholdScoreType = $dbh->prepare($sqlInsertHouseholdScoreType);
				$insertHouseholdScoreType->bindParam(":household_id", $household_id, PDO::PARAM_INT);
				$insertHouseholdScoreType->bindParam(":score_type_id", $type, PDO::PARAM_INT);
				$insertHouseholdScoreType->bindParam(":date", $today, PDO::PARAM_STR);
				$insertHouseholdScoreType->bindParam(":value", $amount = 0, PDO::PARAM_INT);
				
				
				//Iterate over different household score types and check if each exists, and if not insert them into the table then update the score
				foreach($scoreTypes as $key => $value) {
					$type = $value;
					if ($type == 0) {
						$startDate = "2010-01-01";
						$checkHouseholdScoreExist->execute();
						$householdScoreExist = $checkHouseholdScoreExist->fetchAll();
						if (count($householdScoreExist) < 1) {
							$insertHouseholdScoreType->execute();
						}
					} else {
						$startDate = $startOfMonth;
						$checkHouseholdScorExist->execute();
						$householdScoreExist = $checkHouseholdScoreExist->fetchAll();
						if (count($householdScoreExist) < 1) {
							$insertHouseholdScoreType->execute();
						}
					}
				}
			} else {
				echo "Username is taken!";
			}
		} else {
			echo "household_id, username and email_hash must be set to a value and can't be empty, while other values that can and are empty must be null";
		}
		
		
		//Close connection
		$dbh = null;
		
		
	} catch(PDOException $e) {
		echo '<h1>An error has occured.</h1><pre>', $e->getMessage(), '</pre>';
	}
	
function getIfEmpty($post) {
    if (empty($post)) {
		return $nullValue;
	} else {
		return $post;
	}
}
?>