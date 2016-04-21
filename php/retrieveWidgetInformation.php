<?php
	// Connection info for database
	$hostname = 'localhost';
	$username = 'root'; //Temporarily for testing purposes, create a MySQL user for this later
	$password = 'cossmic'; // same as above
	$database = 'CoSSMunity';
	
	// Connection to the database
	try {
		$dbh = new PDO('mysql:host='.$hostname.';dbname='.$database, $username, $password);
				
		
		//TODO Write code to put household_id into the variable below from the clientside.
		$household_id = null;
		
		
		// Notes: Functions like MAX or other such things in MySQL, needs to be defined with the AS if you're going to be able to retrieve them from result set.
		//Fetches the current rank the household is at, for use in the script
		$sqlRetrieveHouseholdHighestRank = "SELECT MAX(rank_rank_id) AS rank
			FROM household_ranks
			WHERE date_obtained IS NOT NULL
			AND household_household_id = :household_household_id";
		$retrieveHouseholdHighestRank = $dbh->prepare($sqlRetrieveHouseholdHighestRank);
		$retrieveHouseholdHighestRank->bindParam(':household_household_id', $household_id = 0, PDO::PARAM_INT);
		$retrieveHouseholdHighestRank->execute();
		$householdHighestRank = $retrieveHouseholdHighestRank->fetchAll(PDO::FETCH_ASSOC);
		$householdHighestRank = $HouseholdHighestRank['rank'];
		echo $rank"<br>";
		
		
		//Fetches the rank information for the users current rank for the widget
		$sqlRetrieveHouseholdRankInformation = "SELECT  rank_id, rank_name, rank_image, requirement
			FROM rank
			WHERE rank_id = :rank_id";
		$retrieveHouseholdRankInformation = $dbh->prepare($sqlRetrieveRankInformation);
		$retrieveHouseholdRankInformation->bindParam(':rank_id', $rank, PDO::PARAM_INT);
		$retrieveHouseholdRankInformation->execute();
		$householdRankInformation = $retrieveHouseholdRankInformation->fetchAll(PDO::FETCH_ASSOC);
		$currentRankRequirement = $householdRankInformation['requirement'];
		echo $currentRankRequirement"<br>";
		echo $jsonHouseholdRankInformation = json_encode($householdRankInformation)"<br>";
		
		
		//Fetches the requirement for the next rank for the household, which will be used for calculations in the script
		$sqlRetrieveNextRankRequirement = "SELECT requirement
			FROM rank
			WHERE rank_id = :rank_id";
		$retrieveNextRankRequirement = $dbh->prepare($sqlRetrieveNextRankRequirement);
		$retrieveNextRankRequirement->bindParam(':rank_id', ++$rank, PDO::PARAM_INT);
		echo $rank"<br>";
		$retrieveNextRankRequirement->execute();
		$nextRankRequirement = $retrieveNextRankRequirement->fetchAll(PDO::FETCH_ASSOC);
		$nextRankRequirement = $nextRankRequirement['requirement'];
		echo $nextRankRequirement"<br>";
		
		
		//Fetches the household total score, which will be used for calculations in the script
		$sqlRetrieveHouseholdTotalScore = "SELECT value
			FROM household_scores
			WHERE score_type_score_type_id = :score_type_score_type_id
			AND household_household_id = :household_household_id";
		$retrieveHouseholdTotalScore = $dbh->prepare($sqlRetrieveHouseholdTotalScore);
		$retrieveHouseholdTotalScore->bindParam(':household_household_id', $household_id, PDO::PARAM_INT);
		$retrieveHouseholdTotalScore->bindParam(':score_type_score_type_id', $score_type_id = 0, PDO::PARAM_INT);
		$retrieveHouseholdTotalScore->execute();
		$householdTotalScore = $retrieveHouseholdTotalScore->fetchAll(PDO::FETCH_ASSOC);
		$householdTotalScore = $householdTotalScore['value'];
		echo $householdTotalScore"<br>";
		
		
		//Calculate the percentage done for the next rank for use in the widget
		$denominator = $nextRankRequirement - $currentRankRequirement;
		$numerator = $householdTotalScore - $currentRankRequirement;
		$percentage = $numerator / $denominator;
		echo $percentage"<br>";
		
		
		//Fetches the households monthly total score for the leaderboard on the widget
		$sqlRetrieveHouseholdsMonthScore = "SELECT username, SUM(value) AS score
			FROM household AS HH
			INNER JOIN household_scores AS HS ON HH.household_id = HS.household_household_id
			WHERE DATE
			BETWEEN :startOfMonth
			AND CURDATE()
			AND NOT score_type_score_type_id = 0
			GROUP BY username
			ORDER BY score DESC";
		$retrieveHouseholdsMonthScore = $dbh->prepare($sqlRetrieveHouseholdsMonthScore);
		$retrieveHouseholdsMonthScore->bindParam(':startOfMonth', $date = date('o-m').'-01', PDO::PARAM_STR);
		$retrieveHouseholdsMonthScore->execute();
		$householdsMonthScore = $retrieveHouseholdsMonthScore->fetchAll(PDO::FETCH_ASSOC);
		echo $jsonHouseholdsMonthScore = json_encode($householdsMonthScore)"<br>";
		
		
		// Close connection
		$dbh = null;
	} catch(PDOException $e) {
		echo '<h1>An error has occured.</h1><pre>', $e->getMessage(), '</pre>';
	}
?>