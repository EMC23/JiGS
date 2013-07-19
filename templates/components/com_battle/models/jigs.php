<?php
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport('joomla.application.component.modellist');

class BattleModelJigs extends JModellist{

	function heartbeat(){
		//	$result_1		= $this->check_factories();
		//	$result_2		= $this->check_reprocessors();
		//	$result_3		= $this->check_mines();
		//	$result_4		= $this->check_farms();
		//	$result_5		= $this->respawn();

		$result			= $this->get_players();
		return $result;
	}


	// This is a test method to be called by a chrontab via the kodaly app

	function populate_players2(){
		//		$user_id= $user['id'];
		//		$user_username= $user['username'];

		$db		= JFactory::getDBO();
		$query		= "INSERT INTO jos_jigs_players2 ( iduser) VALUES (1)";
		$db->setQuery($query);
		$result		= $db->query();
		return;
	}


	function eat()
	{
		$db		= JFactory::getDBO();
		$user		= JFactory::getUser();
		$query		= $db->getQuery(true);

		$query->select('health, money');
		$query->from('#__jigs_players');
		$query->where('iduser = ' . $user->id);
		$db->setQuery($query);

		$result		= $db->loadAssoc();
		$health		= $result['health'];
		$money		= $result['money'];

		//	return json_encode($query);

		if ($money > 10)
		{
			$money	= $money - 10;
			$health	= $health + 10;
			$sql	= "Update #__jigs_players SET money = $money, health = $health WHERE iduser= " . $user->id;
			$db->setQuery($sql);
			$db->query();
			$return	= "success";
		}
		else
		{
			$return	= "broke";
		}

		return $return;
	}

	function update_flags($flags){

		$db		= JFactory::getDBO();
		$user		= JFactory::getUser();
		$flags		= implode( ',', $flags);
		$sql		="UPDATE #__jigs_players SET flags =('$flags') WHERE iduser =". $user->id;
		$db->setQuery($sql);
		$db->query();
		return $result;

	}
	function get_cells(){

		$map		= JRequest::getvar('map');
		$db		= JFactory::getDBO();
		$user		= JFactory::getUser();
		$db->setQuery("SELECT row0,row1,row2,row3,row4,row5,row6,row7 FROM #__jigs_maps WHERE id = ".$map);
		$result		= $db->loadAssocList();
		return $result;

	}

	function get_portals(){

		$map		= JRequest::getvar('map');
		$db		= JFactory::getDBO();
		$user		= JFactory::getUser();
		$db->setQuery("SELECT * FROM #__jigs_portals WHERE from_map =" . $map);
		$result		= $db->loadAssocList();
		return $result;
	}

	function add_message($message_id){

		$db		= JFactory::getDBO();
		$message_id	= int($message_id);
		$user		= JFactory::getUser();
		$db->setQuery("SELECT  messages FROM #__jigs_players WHERE iduser =".$user->id);
		$result		= $db->loadAssocList();

		array_unshift ( $result , $message_id);
		$db->setQuery( "UPDATE  #__jigs_players SET messages = $message WHERE iduser =".$user->id);
		$result		= $db->query();
		return $result;

	}

	function get_messages_old(){
		$db		= JFactory::getDBO();
		$user		= JFactory::getUser();
		$db->setQuery("SELECT messages FROM #__jigs_players WHERE iduser =".$user->id);
		$result		= $db->loadResult();
		$result		= explode(',',$result);

		foreach ($result as $message_id){
			$db->setQuery("SELECT string FROM #__jigs_messages WHERE id =" . $message_id);
			$message_list[]	= $db->loadResult();
		}

		return $message_list;
	}




	function get_messages(){
		$db		= JFactory::getDBO();
		$user		= JFactory::getUser();
		$db->setQuery("SELECT message FROM #__jigs_logs WHERE user_id =".$user->id ." ORDER BY timestamp DESC LIMIT 6");
		$message_list	= $db->loadObjectList();

		return $message_list;
	}


	function get_stats() {
		$db		= JFactory::getDBO();
		$user		= JFactory::getUser();
		//	$test = self::set_final_stats();
		$sql		= "SELECT level, health, strength, intelligence,speed, posx, posy, xp,energy, money, bank, defence, final_defence,
			attack, final_attack, nbr_attacks, nbr_kills, flags FROM #__jigs_players WHERE iduser = " . $user->id;
		$db->setQuery($sql);
		$result		= $db->loadAssocList();

		return $result;
	}


	function get_player() {
		$db		= JFactory::getDBO();
		$user		= JFactory::getUser();
		$test		= self::set_final_stats();
		$db->setQuery("
			SELECT posx, posy, xp, grid, map
			FROM #__jigs_players WHERE iduser =".$user->id);
		$result		= $db->loadAssocList();
		return $result;
	}

	function set_final_stats() {

		$db		= JFactory::getDBO();
		$user		= JFactory::getUser();
		$db->setQuery("Select attack, defence FROM  #__jigs_players WHERE iduser =".$user->id);
		$db->query();
		$result		= $db->loadRow();
		$attack		= $result[0];
		$defence	= $result[1];
		$db->setQuery("
			Select #__jigs_weapon_names.attack, #__jigs_weapon_names.defence FROM #__jigs_players
			LEFT JOIN #__jigs_weapon_names
			ON #__jigs_players.id_weapon = #__jigs_weapon_names.id
			WHERE iduser =".$user->id);
		$db->query();
		$result		= $db->loadRow();
		$weapon_attack	= $result[0];
		$weapon_defence	= $result[1];
		$final_attack	= $attack + $weapon_attack;
		$final_defence	= $defence + $weapon_defence;
		$db->setQuery("UPDATE #__jigs_players SET final_attack = '" . $final_attack. "', final_defence = '" . $final_defence . "'WHERE iduser =".$user->id);
		$db->query();
		return ($result);
	}

	function leave_room(){
		$db		= JFactory::getDBO();
		$user		= JFactory::getUser();
		$query		= "Update #__jigs_players SET active=1 WHERE iduser = $user->id";
		$db->setQuery($query);
		$db->query();
		return true;
	}


	function get_papers() {

		$db		= JFactory::getDBO();
		$user		= JFactory::getUser();
		$db->setQuery("SELECT #__jigs_papers.item_id, #__jigs_paper_names.name, #__jigs_papers.buy_price " .
			"FROM #__jigs_papers " .
			"LEFT JOIN #__jigs_paper_names " .
			"ON #__jigs_papers.item_id = #__jigs_paper_names.id " .
			"WHERE #__jigs_papers.player_id =".$user->id);
		$result		= $db->loadAssocList();
		return $result;

	}


	function get_shop_papers() {

		$db		= JFactory::getDBO();
		$user		= JFactory::getUser();
		$building_id	= JRequest::getvar('building_id');
		$db->setQuery("SELECT #__jigs_papers.item_id, " .
			"#__jigs_papers.sell_price, " . 
			"#__jigs_paper_names.name " .
			"FROM #__jigs_papers LEFT JOIN  #__jigs_paper_names ON #__jigs_papers.item_id = #__jigs_paper_names.id " .
			"WHERE #__jigs_papers.player_id =" . $building_id);
		$result		= $db->loadAssocList();
		return $result;

	}

	function get_blueprints() {
		$db		= JFactory::getDBO();
		$user		= JFactory::getUser();
		$db->setQuery("SELECT #__jigs_blueprints.id, #__jigs_objects.name " .
			"FROM #__jigs_blueprints " .
			"LEFT JOIN #__jigs_objects " .
			"ON #__jigs_blueprints.object = #__jigs_objects.id " .
			"WHERE #__jigs_blueprints.user_id =".$user->id);
		$result		= $db->loadAssocList();
		return $result;

	}

	function get_shop_blueprints() {

		$db		= JFactory::getDBO();
		$user		= JFactory::getUser();
		$building_id	= JRequest::getvar('building_id');
		$db->setQuery("SELECT #__jigs_blueprints.object, " .
			"#__jigs_blueprints.sell_price, " . 
			"#__jigs_objects.name " .
			"FROM #__jigs_blueprints LEFT JOIN  #__jigs_objects ON #__jigs_blueprints.object = #__jigs_objects.id " .
			"WHERE #__jigs_blueprints.user_id =" . $building_id);
		$result		= $db->loadAssocList();
		return $result;

	}


	function get_shop_clothing() {

		$db		=& JFactory::getDBO();
		$user		=& JFactory::getUser();
		$building_id	= JRequest::getvar('building_id');
		$db->setQuery("SELECT #__jigs_clothing.item_id, " .
			"#__jigs_clothing.sell_price, " . 
			"#__jigs_clothing_names.name " .
			"FROM #__jigs_clothing LEFT JOIN  #__jigs_clothing_names ON #__jigs_clothing.item_id = #__jigs_clothing_names.id " .
			"WHERE #__jigs_clothing.player_id =" . $building_id);
		$result		= $db->loadAssocList();
		return $result;

	}

	function get_shop_spells() {

		$db		= JFactory::getDBO();
		$user		= JFactory::getUser();
		$building_id	= JRequest::getvar('building_id');
		$db->setQuery("SELECT #__jigs_spells.item_id, " .
			"#__jigs_spells.sell_price, " . 
			"#__jigs_spell_names.name " .
			"FROM #__jigs_spells LEFT JOIN  #__jigs_spell_names ON #__jigs_spells.item_id = #__jigs_spell_names.id " .
			"WHERE #__jigs_spells.player_id =" . $building_id);
		$result		= $db->loadAssocList();
		return $result;
	}

	function get_shop_weapons() {

		$db		=& JFactory::getDBO();
		$user		=& JFactory::getUser();
		$building_id	= JRequest::getvar('building_id');
		$db->setQuery("SELECT #__jigs_weapons.item_id, " .
			"#__jigs_weapon_names.sell_price, " . 
			"#__jigs_weapon_names.name " .
			"FROM #__jigs_weapons LEFT JOIN  #__jigs_weapon_names ON #__jigs_weapons.item_id = #__jigs_weapon_names.id " .
			"WHERE #__jigs_weapons.player_id =" . $building_id);
		$result		= $db->loadAssocList();
		return $result;
	}


	function get_inventory_to_sell() {

		$db		=& JFactory::getDBO();
		$user		=& JFactory::getUser();
		$building_id	= JRequest::getvar('building_id');
		$db->setQuery("SELECT #__jigs_inventory.item_id, " .
			"#__jigs_objects.name, " .
			"#__jigs_shop_prices.buy_price " .
			"FROM #__jigs_inventory " .

			"LEFT JOIN #__jigs_objects " .
			"ON #__jigs_inventory.item_id = #__jigs_objects.id " .

			"LEFT JOIN #__jigs_shop_prices " .
			"ON #__jigs_inventory.item_id = #__jigs_shop_prices.item_id " .

			"WHERE #__jigs_inventory.player_id = ". $user->id .
			" AND #__jigs_shop_prices.shop_id = " . $building_id ."
			");
		$result = $db->loadAssocList();
		return $result;
	}
	function get_metals_to_sell() {

		$db		= JFactory::getDBO();
		$user		= JFactory::getUser();
		$building_id	= JRequest::getvar('building_id');

		$query		= "SELECT #__jigs_metals.item_id, 
			#__jigs_metal_names.name, 
			#__jigs_shop_metal_prices.buy_price 
			FROM #__jigs_metal_names 
			LEFT JOIN #__jigs_metals 
			ON #__jigs_metals.item_id = #__jigs_metal_names.id 
			LEFT JOIN #__jigs_shop_metal_prices 
			ON #__jigs_metals.item_id = #__jigs_shop_metal_prices.item_id 
			WHERE #__jigs_metals.player_id =  $user->id 
			AND #__jigs_shop_metal_prices.shop_id = " . $building_id ;

		$db->setQuery($query);
		$result = $db->loadAssocList();
		return $result;
	}

	function get_metals_for_sale() {

		$db		=& JFactory::getDBO();
		$user		=& JFactory::getUser();
		$building_id	=  JRequest::getvar('building_id');

		$db->setQuery("SELECT #__jigs_inventory.item_id, " .
			"#__jigs_objects.name, " .
			"#__jigs_shop_prices.buy_price " .
			"FROM #__jigs_inventory " .

			"LEFT JOIN #__jigs_objects " .
			"ON #__jigs_inventory.item_id = #__jigs_objects.id " .

			"LEFT JOIN #__jigs_shop_prices " .
			"ON #__jigs_inventory.item_id = #__jigs_shop_prices.item_id " .

			"WHERE #__jigs_inventory.player_id = ". $user->id .
			" AND #__jigs_shop_prices.shop_id = " . $building_id ."
			");
		$result = $db->loadAssocList();
		return $result;
	}



	function get_crystals() {

		$db		= JFactory::getDBO();
		$user		= JFactory::getUser();
		$building_id	= JRequest::getvar('building_id');

		$db->setQuery(
			"SELECT #__jigs_crystals.item_id, " .
			"#__jigs_crystal_names.name, " .
			"#__jigs_crystal_prices.buy_price " .
			"FROM #__jigs_crystals " .

			"LEFT JOIN #__jigs_crystal_names " .
			"ON #__jigs_crystals.item_id = #__jigs_crystal_names.id " .

			"LEFT JOIN #__jigs_crystal_prices " .
			"ON #__jigs_crystals.item_id = #__jigs_crystal_prices.item_id " .

			"WHERE #__jigs_crystals.player_id = " . $user->id .
			" AND #__jigs_crystal_prices.shop_id = " . $building_id);

		$result = $db->loadAssocList();
		return $result;
	}



	function get_inventory2() {

		$db		= JFactory::getDBO();
		$user		= JFactory::getUser();
		$building_id	= JRequest::getvar('building_id');

		$db->setQuery("SELECT DISTINCT 
			#__jigs_inventory.item_id, " .
			"#__jigs_objects.name " .
			"FROM #__jigs_inventory " .
			"LEFT JOIN #__jigs_objects " .
			"ON #__jigs_inventory.item_id = #__jigs_objects.id " .
			"WHERE #__jigs_inventory.player_id = ". $user->id  
		);
		$result		= $db->loadObjectList();
		foreach ($result as $row){
			$sql	="SELECT id FROM #__jigs_inventory WHERE #__jigs_inventory.player_id = $user->id  and #__jigs_inventory.item_id = $row->item_id";
			$db->setQuery($sql);
			$quantity	= $db->loadAssocList();
			$row->quantity	= count($quantity);
		}
		return $result;
	}





	function get_metals2() {

		$db		= JFactory::getDBO();
		$user		= JFactory::getUser();

		$db->setQuery("SELECT #__jigs_metals.item_id, " .
			"#__jigs_metals.quantity, " .
			"#__jigs_metal_names.name " .

			"FROM #__jigs_metals " .
			"LEFT JOIN  #__jigs_metal_names " .
			"ON #__jigs_metals.item_id = #__jigs_metal_names.id " .
			"WHERE #__jigs_metals.player_id =" . $user->id);

		$result		= $db->loadAssocList();
		return $result;
	}

	function get_crystals2() {

		$db		= JFactory::getDBO();
		$user		= JFactory::getUser();

		$db->setQuery("SELECT #__jigs_crystals.item_id, " .
			"#__jigs_crystal_names.name, #__jigs_crystals.quantity " .

			"FROM #__jigs_crystals " .
			"LEFT JOIN  #__jigs_crystal_names " .
			"ON #__jigs_crystals.item_id = #__jigs_crystal_names.id " .
			"WHERE #__jigs_crystals.player_id =" . $user->id);

		$result		= $db->loadAssocList();
		return $result;
	}


	function get_skills() {

		$db		=& JFactory::getDBO();
		$user		=& JFactory::getUser();

		$db->setQuery("SELECT * FROM #__jigs_skills WHERE #__jigs_skills.iduser =".$user->id);
		$result1 = $db->loadObject();

		for ($i= 1;$i< 9;$i++){
			$db->setQuery("SELECT name FROM #__jigs_skill_names WHERE #__jigs_skill_names.id = '". $result1->skill_ . $i ."'" );
			$result	= $db->loadresult();
			$all[$i]= $result;
		}
		return $all ;
	}


	function get_clothing() {

		$db		=& JFactory::getDBO();
		$user		=& JFactory::getUser();

		$db->setQuery("SELECT #__jigs_clothing.item_id, #__jigs_clothing_names.name " .
			"FROM #__jigs_clothing " .
			"LEFT JOIN #__jigs_clothing_names ON #__jigs_clothing.item_id =  #__jigs_clothing_names.id " .
			"WHERE #__jigs_clothing.player_id =".$user->id);

		$result		= $db->loadAssocList();
		return $result;
	}

	function get_weapon() {

		$db		=& JFactory::getDBO();
		$user		=& JFactory::getUser();
		$char		= 62;

		$db->setQuery(
			"SELECT #__jigs_weapon_names.* " .
			"FROM #__jigs_players " .
			"LEFT JOIN #__jigs_weapon_names ON #__jigs_players.id_weapon = #__jigs_weapon_names.id " .
			"WHERE #__jigs_players.iduser = " . $user->id);

		$result = $db->loadRow();

		$image = '<a rel="{handler: \'iframe\', size: {x: 640, y: 480}}" href="index.php?option=com_battle&view=weapons&id=' .  $user->id . ' "> ' .
			'<img src="components/com_battle/images/weapons/' . $result[1] . '"></a><br>' .
			'Id: ' . $result[0] .'| Bullets per clip: ' . $result[2] .
			'<br>Attack: ' . $result[3] .'| Defence: ' . $result[4] .
			'<br>Precision: ' . $result[5] .'| Detente: ' . $result[6] .
			'<br>Price: ' . $result[7] .'| Ammunition Price: ' . $result[8] 
			;

		return $image;
	}


	function get_weapons() {

		$db		=& JFactory::getDBO();
		$user		=& JFactory::getUser();

		$db->setQuery("SELECT #__jigs_weapons.item_id, #__jigs_weapon_names.name, #__jigs_weapon_names.sell_price " .
			" FROM #__jigs_weapons " .
			" LEFT JOIN #__jigs_weapon_names ON #__jigs_weapons.item_id =  #__jigs_weapon_names.id " .
			"WHERE #__jigs_weapons.player_id =".$user->id);

		$result = $db->loadAssocList();
		return $result;
	}

	function get_weapons2() {

		$db		= JFactory::getDBO();
		$user		= JFactory::getUser();

		$db->setQuery("SELECT #__jigs_weapons.item_id, #__jigs_weapon_names.name " .
			"FROM #__jigs_weapons " .
			"LEFT JOIN #__jigs_weapon_names ON #__jigs_weapons.item_id =  #__jigs_weapon_names.id " .
			"WHERE #__jigs_weapons.player_id =".$user->id);
		$result		= $db->loadAssocList();
		return $result;
	}



	function get_spells() {

		$db		= JFactory::getDBO();
		$user		= JFactory::getUser();

		$db->setQuery("SELECT #__jigs_spells.item_id, #__jigs_spell_names.name " .
			"FROM #__jigs_spells LEFT JOIN #__jigs_spell_names ON #__jigs_spells.item_id =#__jigs_spell_names.id  " .
			"WHERE #__jigs_spells.player_id =".$user->id);

		$result		= $db->loadAssocList();
		return $result;
	}

	function get_software() {

		$db		= JFactory::getDBO();
		$user		= JFactory::getUser();

		$db->setQuery("SELECT * " .
			"FROM #__jigs_software " .
			"WHERE #__jigs_software.iduser =".$user->id);

		$result		= $db->loadRow();
		return $result;
	}

	function get_shop_software() {

		$db		= JFactory::getDBO();
		$user		= JFactory::getUser();
		$building_id	= JRequest::getvar(building_id);

		$db->setQuery("SELECT  " .
			"quantity_1 ,	price_1 ,	quantity_2 ,price_2 ,	quantity_3 	,price_3, " .
			"quantity_4 ,	price_4 ,	quantity_5 ,price_5 ,	quantity_6 ,	price_6 , " .
			"quantity_7 ,	price_7 ,	quantity_8 ,	price_8		".	
			"FROM #__jigs_software " .
			"WHERE #__jigs_software.iduser =".$building_id);

		$result		= $db->loadRow();
		return $result;
	}
	function get_property() {

		$db		= JFactory::getDBO();
		$user		= JFactory::getUser();

		$db->setQuery("SELECT image " .
			"FROM #__jigs_buildings " .
			"WHERE #__jigs_buildings.proprio  =".$user->id);

		$result		= $db->loadAssocList();
		return $result;
	}


	function buy() {
		$db		= JFactory::getDBO();
		$user		= JFactory::getUser();
		$building_id	= JRequest::getvar(building_id);
		$item		= JRequest::getvar(item);

		$db->setQuery("SELECT money FROM #__jigs_players WHERE iduser =" . $user->id);

		$player_money	= $db->loadResult();

		$db->setQuery("SELECT sell_price FROM #__jigs_shop_prices WHERE #__jigs_shop_prices.item_id = " . $item .
			" AND #__jigs_shop_prices.shop_id = " . $building_id);

		$sell_price	= $db->loadResult();
		if ($player_money > $sell_price) {
			$player_money	= $player_money - $sell_price;

			$db->setQuery( "INSERT INTO #__jigs_inventory (player_id , item_id) VALUES (" . $user->id . " , " . $item . ")");
			$result		= $db->query();

			$db->setQuery("UPDATE #__jigs_players SET #__jigs_players.money = " . $player_money . " WHERE iduser = " . $user->id );
			$result2	= $db->query();
			$result3	='true';

			return $player_money;
		}
	}

	function buy_metal() {

		$db			= JFactory::getDBO();
		$user			= JFactory::getUser();
		$building_id		= JRequest::getvar(building_id);
		$item			= JRequest::getvar(metal);
		$db->setQuery("SELECT money FROM #__jigs_players WHERE iduser =" . $user->id);
		$player_money		= $db->loadResult();
		$db->setQuery("SELECT sell_price FROM #__jigs_shop_metal_prices WHERE #__jigs_shop_metal_prices.item_id = " . $item .
			" AND #__jigs_shop_metal_prices.shop_id = " . $building_id);
		$sell_price = $db->loadResult();

		if ($player_money > $sell_price) {
			$player_money	= $player_money - $sell_price;

			$sql		= "INSERT INTO #__jigs_metals (player_id , item_id,quantity) 
				VALUES (" . $user->id . " , " . $item . ",1) 
				ON DUPLICATE KEY UPDATE quantity = quantity + 1";

			$db->setQuery($sql);
			$result		= $db->query();
			$sql		= "UPDATE #__jigs_players SET #__jigs_players.money = " . $player_money . " WHERE iduser = " . $user->id;
			$db->setQuery($sql);

			$result2	= $db->query();
			$result3	= 'true';

			return $player_money;
		}

		return $player_money;
	}

	///// ADD UP ALL ENERGY FROM ALL BATTERIES FOR ONE USER /////
	function get_total_energy($id)
	{
		$batteries	= $this->get_all_energy($id);
		$total		= 0;
		foreach ($batteries as $battery)
		{
			$total	= $total + $battery->units;
		}
		return $total;
	}

	///// GET ALL BATTERIES FOR ONE USER /////
	function get_all_energy($id)
	{
		$db		= JFactory::getDBO();
		$sql		= "SELECT * FROM #__jigs_batteries WHERE iduser = " . $id;
		$db->setQuery($sql);
		$_all_energy	= $db->loadObjectList();
		return $_all_energy;
	}

	///// TAKE ENERGY FROM USER'S BATTERIES UNTIL $energy_units_required IS REACHED /////
	function use_battery($id, $energy_units_required)
	{
		$db		= JFactory::getDBO();
		$user		= JFactory::getUser();
		$message	= "Energy Required : " . $energy_units_required;
		$this->sendFeedback($user->id,$message);

		$batteries	= $this->get_all_energy($id);
		$total		= $this->get_total_energy($id);
		$message	= "Total Energy available : " . $total;
		$this->sendFeedback($user->id,$message);

		if ($total < $energy_units_required)
		{
			$message="not enough energy";
			$this->sendFeedback($user->id,$message);
			return false;
		}

		$i=1;		
		foreach ($batteries as $battery)
		{
			if($energy_units_required > 0)
			{
				if ($energy_units_required < $battery->units)
				{
					$db			= JFactory::getDBO();
					$battery->units 	= $battery->units - $energy_units_required;
					$message		= $energy_units_required . " unit(s) deducted from  battery " . $i ;
					$energy_units_required	= 0;
					$this->sendFeedback($user->id,$message);
				}
				else
				{
					$energy_units_required	= $energy_units_required - $battery->units;
					$message 		.= $battery->units . " unit(s) deducted from  battery " . $i . "<br/>";
					$battery->units		= 0;
					$message 		.= "zero units remaining in battery " . $i ."</br>";
					$this->sendFeedback($user->id,$message);
				}

				$sql	= "UPDATE #__jigs_batteries SET units = " . $battery->units . " WHERE id = " . $battery->id;
				$db->setQuery($sql);
				$result	= $db->query();
			}
			else
			{
				$message= "energy transer complete";
				$this->sendFeedback($user->id,$message);
				break;
			}
			$i++;
		}

		$total		= $this->get_total_energy($id);
		$message	= $total . " remaining energy units";
		$this->sendFeedback($user->id,$message);
		return true;
	}

	/// GIVE BATTERY FROM USER TO BUILDING ///
	function swap_battery()
	{
		$db		= JFactory::getDBO();
		$building_id	= JRequest::getvar('building_id');
		$id		= JRequest::getvar('id');
		$sql		= "UPDATE #__jigs_batteries SET iduser = $building_id where id = $id";
		$db->setQuery($sql);
		$result		= $db->query();	
		return $sql;
	}

	function charge_battery()
	{
	}

	///// PLAYER BUYS BATTERY FROM THIN AIR. GETS 100 UNITS MONEY DEDUCTED /////
	function buy_battery()
	{
		$db		= JFactory::getDBO();
		$user		= JFactory::getUser();
		$building_id	= JRequest::getvar('building_id');

		$db->setQuery("SELECT money FROM #__jigs_players WHERE iduser =" . $user->id);
		$player_money	= $db->loadResult();
		$sell_price	= 100;

		if ($player_money > $sell_price)
		{
			$player_money	= $player_money - $sell_price;
			$sql		= "INSERT INTO #__jigs_batteries (charge_percentage,capacity,iduser) VALUES (100,10,$user->id)";
			$db->setQuery($sql);
			$result		= $db->query();

			$db->setQuery("UPDATE #__jigs_players SET #__jigs_players.money = " . $player_money . " WHERE iduser = " . $user->id );
			$result2	= $db->query();
			$result3	= 'true';
			return $player_money;
		}
		return $player_money;
	}

	///// PLAYER SELLS BATTERY TO BUILDING /////
	function sell_battery()
	{
		$db		= JFactory::getDBO();
		$user		= JFactory::getUser();
		$building_id	= JRequest::getvar('building_id');

		$db->setQuery("SELECT money FROM #__jigs_players WHERE iduser =" . $user->id);
		$player_money	= $db->loadResult();
		$sell_price	= 90;
		$player_money	= $player_money + $sell_price;

		$sql2		= "UPDATE #__jigs_batteries SET iduser = $building_id WHERE iduser = " . $user->id . " LIMIT 1";
		$db->setQuery($sql2);
		$result		= $db->query();

		$db->setQuery("UPDATE #__jigs_players SET #__jigs_players.money = " . $player_money . " WHERE iduser = " . $user->id );
		$result2	= $db->query();
		$result3	= 'true';

		return $sql2;
	}

	///// SELECT ALL BATTERIES FOR A USER /////
	function get_batteries()
	{
		$db		= JFactory::getDBO();
		$user		= JFactory::getUser();
		$sql		= "SELECT * FROM #__jigs_batteries WHERE iduser =" . $user->id;
		$db->setQuery($sql);
		$result		= $db->loadRowlist();
		//return $sql;
		return $result;
	}



	function get_character_inventory($id)
	{
		$db		= JFactory::getDBO();
		$user		= JFactory::getUser();

		$db->setQuery("SELECT #__jigs_inventory.item_id, " .
			"#__jigs_objects.name " .
			"FROM #__jigs_inventory " .
			"LEFT JOIN #__jigs_objects " .
			"ON #__jigs_inventory.item_id = #__jigs_objects.id " .
			"WHERE #__jigs_inventory.player_id =".$id);
		$result		= $db->loadAssocList();
		return $result;
	}

	function get_players_view()
	{
		$id		= substr(JRequest::getvar('id'), 5);
		$people		= JTable::getInstance('players', 'Table');
		$people->load($id);
		$inv		= $this->get_character_inventory($id);
		$db		= JFactory::getDBO();
		$query		= "SELECT #__comprofiler.avatar FROM #__comprofiler WHERE #__comprofiler.id =" . $id;
		$db->setQuery($query);
		$people->avatar	= $db->loadresult();

		$text ='<div id="screen_grid" style=" width: 400px; height:400px; margin: 0 auto; text-align:center;
		background:#000; float:left; position:relative; left:0px; top:0px;">
			<div id="profile_" class="clearfix">
			<div class="name">' . $people->username . '</div>
			<div class="desc">
			<img src="/images/comprofiler/' . $people->avatar .'" class="thumbnail" alt="' . $people->username .
			'" title="<' .  $people->username .'" width="100" height="100" id="character_image" />
			<div class="stats">
			<table class="stats" >
			<tr>
			<th scope="row">ID</th>
			<td>'.$id .'</td>
			</tr>
			<tr>
			<th scope="row">Name</th>
			<td>'. $people->username .'</td>
			</tr>
			<tr>		
			<th scope="row">Money</th>
			<td>'. $people->money .'</td>
			</tr>
			</table>
			</div><!-- end stats -->


			<p class="desc">'. $people->comment .'</p>
			</div><!-- end desc -->
			<div class="vitals">
			<div class="label">Experience:</div>
			<div class="gauge"><div id="xp"><span>'. $people->xp .'</span></div></div>
			<div class="label">Intelligence:</div>
			<div class="gauge"><div id="intel"><span>'. $people->intelligence .'</span></div></div>
			<div class="label">Strength:</div>
			<div class="gauge"><div id="strength"><span>'. $people->strength  .'</span></div></div>
			<div class="label">Health:</div>
			<div class="gauge"><div id="health" style="width:'. $people->health .'%"><span id="health">'. $people->health .
			'</span></div></div>
			</div><!-- end vitals -->
			</div><!-- end profile -->

			<div id="_inventory" class="clearfix">
			<div class="name">Inventory</div>
			';

/*
foreach ($inv as $inv_object)
{
$text .= "<br>" . $inv_object["name"] ;
}

 */
		$text .='</div><!-- end inventory -->

			<div id="action" class="clearfix">
			<!-- <div class="recruit"><a class="recruit" href="#">Recruit</a></div> --> 
			<div class="shoot"><a onclick="shoot_person(' . $id . ')" id="shoot" >Shoot</a></div>
			<div class="kick"><a onclick="kick_person('. $id . ')" id="kick" >Kick</a> </div>
			<div class="punch"><a onclick="punch_person('. $id . ')" id="punch">Punch</a> </div>
			<div class="talk"><a onclick="talk_person('. $id . ')" id="talk">Talk</a> </div>
			<!--   <div class="bribe"><a class="bribe" href="#">Bribe</a></div>
			<div class="rob"><a class="rob" href="#">Rob</a></div>
			<div class="talk"><a class="talk" href="#">Talk</a></div>--> 
			</div>

			</div>
			';
		return $text;
	}	

	function get_character_view()
	{
		$id= JRequest::getvar('id');
		$people = JTable::getInstance('people', 'Table');
		$people->load($id);
		$inv = $this->get_character_inventory($id);
		$text ='<div id="screen_grid" style=" width: 400px; height:400px; margin: 0 auto; text-align:center; background:#000; float:left; position:relative; left:0px; top:0px;">

			<div id="profile_" class="clearfix">
			<div class="name">' . $people->name . '</div>
			<div class="desc">
			<img src="/components/com_battle/images/ennemis/' . $people->image . '" class="thumbnail" alt="'. $people->name . ' " title="' . $people->name .'" width="100" height="100" id="character_image" />
			<div class="stats">
			<table class="stats" >
			<tr>
			<th scope="row">ID</th>
			<td>'.$people->id .'</td>
			</tr>
			<tr>
			<th scope="row">Name</th>
			<td>'. $people->name .'</td>
			</tr>
			<tr>		
			<th scope="row">Money</th>
			<td>'. $people->money .'</td>
			</tr>
			<tr>		
			<th scope="row">XP</th>
			<td>'. $people->xp .'</td>
			</tr>
			<tr>		
			<th scope="row">Intelligence</th>
			<td>'. $people->intelligence .'</td>
			</tr>
			<tr>		
			<th scope="row">Strength</th>
			<td>'. $people->strength .'</td>
			</tr>

			</table>
			</div><!-- end stats -->

			<p class="desc">'. $people->comment .'</p>
			</div><!-- end desc -->
			<div class="vitals">
			<!--<div class="label">Experience:</div>
			<div class="gauge"><div id="xp"><span>'. $people->xp .'</span></div></div>
			<div class="label">Intelligence:</div>
			<div class="gauge"><div id="intel"><span>'. $people->intelligence .'</span></div></div>
			<div class="label">Strength:</div>
			<div class="gauge"><div id="strength"><span>'. $people->strength  .'</span></div></div>-->
			<div class="label">Health:</div>
			<div class="gauge"><div id="health" style="width:'. $people->health .'%"><span id="health">'. $people->health .'</span></div></div>
			</div><!-- end vitals -->
			</div><!-- end profile -->

			<!--<div id="_inventory" class="clearfix">
			<div class="name">Inventory</div>
			';

/*
foreach ($inv as $inv_object)
{
$text .= "<br>" . $inv_object["name"] ;
}

 */
		$text .='</div> --><!-- end inventory -->

			<div id="action" class="clearfix">
			<!-- <div class="recruit"><a class="recruit" href="#">Recruit</a></div> --> 
			<div class="shoot"><a onclick="shoot(' . $people->id . ')" id="shoot" >Shoot</a></div>
			<div class="kick"><a onclick="kick('. $people->id . ')" id="kick" >Kick</a> </div>
			<div class="punch"><a onclick="punch('. $people->id . ')" id="punch">Punch</a> </div>
			<div class="talk"><a onclick="talk_person('. $id . ')" id="talk">Talk</a> </div>
			<!--   <div class="bribe"><a class="bribe" href="#">Bribe</a></div>
			<div class="rob"><a class="rob" href="#">Rob</a></div>
			<div class="talk"><a class="talk" href="#">Talk</a></div>--> 
			</div>

			</div>
			';

		return $text;
	}

	function buy_weapon()
	{
		$db			= JFactory::getDBO();
		$user			= JFactory::getUser();
		$building_id		= JRequest::getvar(building_id);
		$item			= JRequest::getvar(item);
		$db->setQuery("SELECT money FROM #__jigs_players WHERE iduser =" . $user->id);
		$player_money		= $db->loadResult();
		$db->setQuery("SELECT sell_price FROM #__jigs_weapon_names WHERE #__jigs_weapon_names.id = " . $item );
		$sell_price		= $db->loadResult();
		if ($player_money > $sell_price) {
			$player_money	= $player_money - $sell_price;
			$db->setQuery( "INSERT INTO #__jigs_weapons (player_id , item_id) VALUES (" . $user->id . " , " . $item . ")"
			);

			$result		= $db->query();
			$db->setQuery("UPDATE #__jigs_players SET #__jigs_players.money = " . $player_money . " WHERE iduser = " . $user->id );
			$result2	= $db->query();
			$result3	='true';
			return $player_money;
		}
	}

	function buy_crystals()
	{
		$db			= JFactory::getDBO();
		$user			= JFactory::getUser();
		$building_id		= JRequest::getvar(building_id);
		$item			= JRequest::getvar(item);
		$db->setQuery("SELECT money FROM #__jigs_players WHERE iduser =".$user->id);
		$player_money		= $db->loadResult();
		$db->setQuery("SELECT sell_price FROM #__jigs_crystals WHERE #__jigs_crystals.id =".$item);
		$sell_price		= $db->loadResult();

		if ($player_money > $sell_price)
		{
			$player_money	= $player_money - $sell_price;
			$db->setQuery( "INSERT INTO #__jigs_crystals (player_id , item_id) VALUES (" . $user->id . " , " . $item . ")");
			$result		= $db->query();
			$db->setQuery("UPDATE #__jigs_players SET #__jigs_players.money = " . $player_money . " WHERE iduser = " . $user->id );
			$result2	= $db->query();
			$result3	= 'true';	
			$result		= $db->loadRow();
			return $player_money;
		}
	}

	function buy_papers()
	{
		$db			= JFactory::getDBO();
		$user			= JFactory::getUser();
		$building_id		= JRequest::getvar(building_id);
		$item			= JRequest::getvar(item);

		$db->setQuery("SELECT money FROM #__jigs_players WHERE iduser =".$user->id);
		$player_money		= $db->loadResult();

		$db->setQuery("SELECT sell_price FROM #__jigs_papers WHERE #__jigs_papers.id =".$item);
		$sell_price		= $db->loadResult();

		if ($player_money > $sell_price)
		{
			$player_money = $player_money - $sell_price;

			$db->setQuery( "INSERT INTO #__jigs_papers (player_id , item_id) VALUES (" . $user->id . " , " . $item . ")");
			$result = $db->query();
			$db->setQuery("UPDATE #__jigs_players SET #__jigs_players.money = " . $player_money . " WHERE iduser = " . $user->id );
			$result2 = $db->query();
			$result3='true';	$result = $db->loadRow();

			return $player_money;
		}
	}

	function buy_blueprints()
	{
		$db		= JFactory::getDBO();
		$user		= JFactory::getUser();
		$building_id	= JRequest::getvar(building_id);
		$item		= JRequest::getvar(item);
		$db->setQuery("SELECT money FROM #__jigs_players WHERE iduser =".$user->id);
		$player_money	= $db->loadResult();
		$db->setQuery("SELECT sell_price FROM #__jigs_blueprints WHERE #__jigs_blueprints.id =".$item);
		$sell_price	= $db->loadResult();

		if ($player_money > $sell_price)
		{
			$player_money	= $player_money - $sell_price;
			$db->setQuery( "INSERT INTO #__jigs_blueprints (user_id, object) VALUES ( $user->id  ,  $item )" );
			$result		= $db->query();
			$db->setQuery("UPDATE #__jigs_players SET #__jigs_players.money = " . $player_money . " WHERE iduser = " . $user->id );
			$result2	= $db->query();
			return $player_money;
		}
	}

	function buy_building()
	{
		$db		= JFactory::getDBO();
		$user		= JFactory::getUser();
		$building_id	= JRequest::getvar(building_id);
		$db->setQuery("SELECT money FROM #__jigs_players WHERE iduser =".$user->id);
		$player_money	= $db->loadResult();
		$db->setQuery("SELECT price FROM #__jigs_buildings WHERE #__jigs_buildings.id =".$building_id);
		$sell_price	= $db->loadResult();

		// If the Player has enough money
		if ($player_money >= $sell_price)
		{
			// player loses cost of building
			$player_money = $player_money - $sell_price;
			
			$this->buy_building_award('building');
			$this->buy_building_award('farm');
			$this->buy_building_award('factory');
			$this->buy_building_award('mine');

			// player gets building
			$db->setQuery("UPDATE #__jigs_buildings SET owner = $user->id WHERE #__jigs_buildings.id = " . $building_id);
			$result = $db->query();

			// update new players cash in hand to database
			$db->setQuery("UPDATE #__jigs_players SET money = " . $player_money . " WHERE iduser = " . $user->id );
			$result = $db->query();
			$message ="You have bought this building";
		}

		// player does not have enough money
		else
		{
			$message ="You do not have enough cash to buy this building";
		}
		$this->sendFeedback($user->id,$message);

		return $result;
	}

	private function buy_building_award($type)
	{
		$db	= JFactory::getDBO();
		$user	= JFactory::getUser();
		$query = "SELECT COUNT(*) FROM #__jigs_buildings WHERE owner = ".$user->id;

		switch($type)
		{
		case 'factory':
			$msg = "You bought your first factory";
			$query .= " AND type = 'factory'";
			break;

		case 'farm':
			$msg = "You bought your first farm";
			$query .= " AND type = 'farm'";
			break;

		case 'mine':
			$msg = "You bought your first mine";
			$query .= " AND type = 'mine'";
			break;

		default:
			$msg = "You bought your first building";
			break;
		}
		
		$db->setQuery($query);
		if(0 == $db->loadResult())
		{
			$model =& JModel::getInstance('award','BattleModel');
			$awardNameId = $model->get_award_id($msg);

			if(null == $awardNameId)
			{
				$awardNameId = $model->insert_award_name($msg);
			}

			$model->insert_award($awardNameId);
		}

	}

	function sell()
	{
		$db		= JFactory::getDBO();
		$user		= JFactory::getUser();
		$building_id	= JRequest::getvar('building_id');
		$item		= JRequest::getvar('item');
		$db->setQuery("SELECT money FROM #__jigs_players WHERE iduser =".$user->id);
		$player_money	= $db->loadResult();
		$db->setQuery("SELECT buy_price FROM #__jigs_shop_prices WHERE item_id = ". $item . " AND shop_id = " . $building_id );
		$buy_price	= $db->loadResult();
		$player_money	= $player_money + $buy_price;
		$db->setQuery("UPDATE #__jigs_players SET #__jigs_players.money = " . $player_money . " WHERE iduser = " . $user->id );
		$result2	= $db->query();
		$db->setQuery("DELETE FROM #__jigs_inventory WHERE #__jigs_inventory.player_id = ".$user->id ." AND item_id=" . $item . " LIMIT 1");
		$result		= $db->query();
		return $result;
	}

	function sell_metal()
	{
		$db 		= JFactory::getDBO();
		$user		= JFactory::getUser();
		$building_id	= JRequest::getvar('building_id');
		$item		= JRequest::getvar('metal');
		$db->setQuery("SELECT money FROM #__jigs_players WHERE iduser = ".$user->id);
		$player_money	= $db->loadResult();
		$db->setQuery("SELECT buy_price FROM #__jigs_shop_metal_prices WHERE item_id = ". $item . " AND shop_id = " . $building_id );
		$buy_price	= $db->loadResult();
		$player_money	= $player_money + $buy_price;
		$db->setQuery("UPDATE #__jigs_players SET #__jigs_players.money = " . $player_money . " WHERE iduser = " . $user->id );
		$result2	= $db->query();
		$sql		= "UPDATE #__jigs_metals SET quantity = quantity - 1 WHERE #__jigs_metals.player_id = " . $user->id .
			" AND item_id= $item ";
		$db->setQuery($sql);
		$result		= $db->query();
		return $result;
	}	


	function sell_weapon()
	{
		$db		= JFactory::getDBO();
		$user		= JFactory::getUser();
		$building_id	= JRequest::getvar('building_id');
		$item		= JRequest::getvar(item);
		$db->setQuery("SELECT money FROM #__jigs_players WHERE iduser =".$user->id);
		$player_money	= $db->loadResult();
		$db->setQuery("SELECT sell_price FROM #__jigs_weapon_names WHERE id = ". $item );
		$buy_price	= $db->loadResult();
		$player_money	= $player_money + ($buy_price/2);
		$db->setQuery("UPDATE #__jigs_players SET #__jigs_players.money = " . $player_money . " WHERE iduser = " . $user->id );
		$result2	= $db->query();
		$db->setQuery("DELETE FROM  #__jigs_weapons WHERE #__jigs_weapons.player_id = ".$user->id ." AND item_id=" . $item . " LIMIT 1");
		$result		= $db->query();
		return $result;
	}


	function sell_crystals()
	{
		$db		= JFactory::getDBO();
		$user 		= JFactory::getUser();
		$building_id	= JRequest::getvar('building_id');
		$item		= JRequest::getvar(item);
		$db->setQuery("SELECT money FROM #__jigs_players WHERE iduser =".$user->id);
		$player_money	= $db->loadResult();
		$db->setQuery("SELECT buy_price FROM #__jigs_crystal_prices WHERE #__jigs_crystal_prices.item_id =".$item);
		$buy_price	= $db->loadResult();
		$player_money 	= $player_money + $buy_price;
		$db->setQuery("UPDATE #__jigs_players SET #__jigs_players.money = " . $player_money . " WHERE iduser = " . $user->id );
		$result2	= $db->query();
		$db->setQuery("DELETE FROM  #__jigs_crystals WHERE #__jigs_crystals.player_id = ".$user->id ." AND item_id=" . $item . " LIMIT 1");
		$result		= $db->query();
		return $result;

	}
	function sell_papers()
	{
		$db		= JFactory::getDBO();
		$user		= JFactory::getUser();
		$building_id	= JRequest::getvar('building_id');
		$item		= JRequest::getvar(item);
		$db->setQuery("SELECT money FROM #__jigs_players WHERE iduser =".$user->id);
		$player_money	= $db->loadResult();
		$db->setQuery("SELECT buy_price FROM #__jigs_papers WHERE #__jigs_papers.id =".$item);
		$buy_price	= $db->loadResult();
		$player_money	= $player_money + $buy_price;
		$db->setQuery("UPDATE #__jigs_players SET #__jigs_players.money = " . $player_money . " WHERE iduser = " . $user->id );
		$result2	= $db->query();
		$db->setQuery("DELETE FROM #__jigs_papers WHERE #__jigs_papers.player_id = ".$user->id ." AND item_id=" . $item . " LIMIT 1");
		$result		= $db->query();
		return $result;
	}

	function get_shop_inventory()
	{
		$db		= JFactory::getDBO();
		$user		= JFactory::getUser();
		$building_id	= JRequest::getvar('building_id');
		$db->setQuery("SELECT #__jigs_shop_prices.item_id, " .
			"#__jigs_objects.name, " .
			"#__jigs_shop_prices.sell_price " .
			"FROM #__jigs_shop_prices " .
			"LEFT JOIN #__jigs_objects " .
			"ON #__jigs_shop_prices.item_id = #__jigs_objects.id " .
			"WHERE #__jigs_shop_prices.shop_id =" . $building_id);
		$result		= $db->loadAssocList();
		return $result;
	}




	function get_battery_slots()
		{
			$db     	= JFactory::getDBO();
			$building	= JRequest::getvar('building_id');
			$now    	= time();
			$factor		= 10;

			$query		= "
			SELECT * 
			FROM #__jigs_batteries
			WHERE iduser = $building
			";

			$db->setQuery($query);
			$batteries = $db->loadAssocList();

		/*	foreach($batteries as $battery)
		{
			$id        = $battery['id'];
			$timestamp = $battery['timestamp'];
			$elapsed   = $now - $timestamp;
			$units     = $battery['units'];
			$max_units = $battery['max_units'];
			$new_units = intVal($elapsed/$factor);

			if($units + $new_units < $max_units)
			{
				$query	= "
					UPDATE #__jigs_batteries SET
					units      = units + $new_units,
					timestamp  = $now
					WHERE id   = $id
					";
			}
			else
			{
				$query	= "
					UPDATE #__jigs_batteries SET
					units      = max_units,
					timestamp  = $now
					WHERE id   = $id
					";
			}


			$db->setQuery($query);
			$db->query();
		
		
		}*/
		return $batteries;
	}

















	function get_shop_metals()
	{
		$db		= JFactory::getDBO();
		$building_id	= JRequest::getvar('building_id');
		$query 		= "
			SELECT #__jigs_shop_metal_prices.item_id, #__jigs_metal_names.name, #__jigs_shop_metal_prices.sell_price
			FROM #__jigs_shop_metal_prices
			LEFT JOIN #__jigs_metals ON #__jigs_shop_metal_prices.item_id = #__jigs_metals.id
			LEFT JOIN #__jigs_metal_names ON #__jigs_metal_names.id = #__jigs_shop_metal_prices.item_id
			WHERE #__jigs_shop_metal_prices.shop_id = $building_id";
		$db->setQuery($query);
		$result		= $db->loadAssocList();
		return $result;
	}

	function get_shop_crystals()
	{
		$db		= JFactory::getDBO();
		$user		= JFactory::getUser();
		$building_id	= JRequest::getvar('building_id');
		$db->setQuery("SELECT #__jigs_crystal_prices.item_id, " .
			"#__jigs_crystal_names.name, " .
			"#__jigs_crystal_prices.sell_price " . 
			"FROM #__jigs_crystal_prices " .
			"LEFT JOIN  #__jigs_crystal_names " .
			"ON #__jigs_crystal_prices.item_id = #__jigs_crystal_names.id " .
			"WHERE #__jigs_crystal_prices.shop_id =" . $building_id);
		$result		= $db->loadAssocList();
		return $result;
	}

	function work_field()
	{
		$db		= JFactory::getDBO();
		$user		= JFactory::getUser();
		$field		= JRequest::getvar('field');
		$building_id	= JRequest::getvar('building_id');
		$now		= time();
		$finished	= $now + 50;

		//$crop	= JRequest::getvar('crop');
		$query		= "SELECT status,crop FROM #__jigs_farms  WHERE building =" . $building_id . " AND field =" . $field;
		$db->setQuery($query);
		$result		= $db->loadRowList();

		$status		= $result[0][0];
		$crop		= $result[0][1];
		$status++;

		$sql		= "INSERT INTO #__jigs_farms (building,field, status,timestamp, crop,finished ) " .  " 
			values  ($building_id,$field, $status,$now , $crop, $finished) 
			ON DUPLICATE KEY UPDATE status =  $status ,timestamp = $now,  crop = $crop , finished = $finished ";

		$db->setQuery($sql);
		if(!$db->query())
		{
			$message="error";
			return $query;
		}
		else
		{
			return true;
		}
	}

	function get_players()
	{
		$db		= JFactory::getDBO();
		$user		= JFactory::getUser();
		$db->setQuery("SELECT map,grid FROM #__jigs_players WHERE iduser =".$user->id);
		$result		= $db->loadRow();
		$map		= $result[0];
		$grid		= $result[1];
		$db->setQuery("SELECT
			#__jigs_players.iduser, 
			#__jigs_players.posx, 
			#__jigs_players.posy, 
			#__comprofiler.avatar
			FROM #__jigs_players 
			LEFT JOIN #__comprofiler ON #__jigs_players.iduser = #__comprofiler.user_id
			WHERE grid ='".$grid."' AND map='".$map."' AND #__jigs_players.iduser !='".$user->id."'");
		$result		= $db->loadAssocList();
		return $result;
	}

	// Called by JiGS.js.php via JSON On successful call the player is moved by mootools
	function save_coordinate()
	{
		$db		= JFactory::getDBO();
		$user		= JFactory::getUser();
		$posx		= JRequest::getvar('posx');
		$posy		= JRequest::getvar('posy');
		$map		= JRequest::getvar('map');
		$grid		= JRequest::getvar('grid');
		$db->setQuery("UPDATE #__jigs_players SET posx='".$posx."',posy='".$posy."',map='".$map."',grid='".$grid.
			"'  WHERE iduser ='".$user->id."'");
		$db->query();
		$result		='success';
		return $result;
	}

	function attack_playa()
	{
		$db		= JFactory::getDBO();
		$player		= JFactory::getUser();
		//$user2	= substr(JRequest::getvar('character'),5);
		$user2		= JRequest::getvar('character');
		$player2	= JFactory::getUser($user2);

		$player->dice	= rand(0, 15);
		$player2->dice	= rand(0, 5);

		$query		= "SELECT health,money,active FROM #__jigs_players WHERE iduser = $player->id";
		$db->setQuery($query);
		$result		= $db->loadRow();
		$player->health	= $result[0];
		$player->money	= $result[1];
		$player->status	= $result[2];		

		$query		= "SELECT health,money,active FROM #__jigs_players WHERE iduser = $user2";
		$db->setQuery($query);
		$result		= $db->loadRow();
		$player2->health= $result[0];
		$player2->money	= $result[1];
		$player2->status= $result[2];		


		if ($player2->status!=1)
		{
			$message	= "This player is inactive. You cannot attack this player at this time<br/>";
		}
		elseif ($player->status !=1)
		{
			$message	= "You are inactive. You cannot attack players at this time<br/>";
		}

		else// roll the dice and let the games begin
		{
			if ($player->dice > $player2->dice)
			{
				$player->health	= $player->health -1;
				$player2->health= $player2->health-30;
				$message	= "You attacked " . $player2->username .
					" and inflicted 30 points of damage. You: $player->health ,Opponent: $player2->health";
			}
			else
			{
				$player->health	= $player->health - 10;
				$player2->health= $player2->health + 10;

				$message	= "You attacked " . $player2->username . " and missed. " . $player2->username .
					" retaliated and inflicted 10 points of damage. You: $player->health ,Opponent: $player2->health ";
			}

			if ($player2->health <= 0)
			{
				$now			= time();

				$player->money	=  $player->money + $player2->money;
				$player2->money	= 0;
				$query 		= "UPDATE #__jigs_players SET active = 3,  grid=1, map= 3, posx = 4, posy=5, empty= 1 , time_killed = $now 
					WHERE iduser = $user2";
				$db->setQuery($query);
				$db->query();

				$query 		= "UPDATE #__jigs_inventory SET #__jigs_inventory.player_id = $player->id 
					WHERE #__jigs_inventory.player_id = $player_id ";
				$db->setQuery($query);
				$db->query();

				$query 		= "UPDATE #__jigs_players SET nbr_kills=nbr_kills+1, money = $player->money 
					WHERE #__jigs_players.iduser = $user->id" ;
				$db->setQuery();
				$db->query();
				$query 		= "UPDATE #__jigs_players SET money = $player2->money WHERE #__jigs_players.iduser = $player->id";
				$db->setQuery($query);
				$db->query();

				$text		= 'Citizen ' . $player2->username  . ' was hospitalised by citizen ' . $user->username ;
				$message	= "You put " . $player2->username . " into hospital.";
				$this->sendWavyLines($text);
			}

			$db->setQuery("UPDATE #__jigs_players SET health='" . $player->health. "'  WHERE iduser ='" . $player->id . "'");
			$db->query();
			$db->setQuery("UPDATE #__jigs_players SET health='" . $player2->health. "'  WHERE iduser ='" . $player2->id . "'");
			$db->query();
		}
		$results[0]	= $player->health;
		$results[1]	= $player2->health;
		$results[2]	= $message;

		$this->sendFeedback($player->id,$message);

		return $results;
	}


	function attack()
	{
		$db		= JFactory::getDBO();
		$user		= JFactory::getUser();
		$character_id	= JRequest::getInt('character');
		$sql= "SELECT iduser, health, money, final_attack, final_defence, ammunition FROM #__jigs_players WHERE iduser = " . $user->id;
		$db->setQuery($sql);
		$player		= $db->loadObject();

		$player->dice	= rand(0, 15);

		$db->setQuery("SELECT id, name, health, money FROM #__jigs_characters WHERE id =" . $character_id);
		$npc		= $db->loadObject();

		$npc->dice=rand(0, 5);
		$attack_type = JRequest::getCmd('type');

		switch ($attack_type)
		{
			///// If Player shoots test shooting skills + speed + dexterity against NPCs speed //////////////
		case 'shoot':
			if ($player->dice > $npc->dice)
			{
				$npc->health	= intval($npc->health - 10);
				$attack_message	= "You shoot " . $npc->name . " and inflict 30 damage points to his health.";
			}
			else
			{
				$attack_message	= "You shoot " . $npc->name . " and miss.";
			}
			$player->ammunition--;
			break;

			//====== If Player kicks, test kicking and other fighting skills + speed + dexterity against NPCs speed ////////
		case 'kick':
			if ($player->dice > $npc->dice)
			{
				$npc->health	= intval($npc->health - 10);
				$attack_message	= "You kick " . $npc->name . "and inflict 30 damage points to his health.";
			}
			else
			{
				$player->health		=	intval($player->health - 10);
				$attack_message	=	"You kick " . $npc->name . "and miss and incur 10 damage points to your health.";
			}
			break;

			// If Player punches, test punch and other fighting skills + speed + dexterity against NPCs speed /////////////
		case 'punch':
			if ($player->dice >= $npc->dice)
			{
				$npc->health	=	intval($npc->health - 20);
				$attack_message	= "You punch " . $npc->name . "and inflict 30 damage points to his health.";
			}
			else
			{
				$player->health	= intval($player->health - 10);
				$attack_message	= "You punch " . $npc->name . "and miss and incur 10 damage points to your health.";
			}
			break;
		}
		////////////////////////////////////////// If NPC is dead ////////////////////////////////////
		if ($npc->health <= 0)
		{
			$npc->health	= 0;
			$this->dead_npc($npc);
		}

		////////////////////////////////////////// If Player is dead ////////////////////////////////////
		if ($player->health <= 0)
		{
			$player->health = 0;
			$this->dead_player($npc->name);		
		}

		/////////////////////////////////////// Now update everybodys stats to database //////////////////
		$sql = "UPDATE #__jigs_players SET health = $player->health, ammunition = $player->ammunition WHERE iduser = $user->id ";
		$db->setQuery($sql);
		$db->query();

		$sql = "UPDATE #__jigs_characters SET health = $npc->health WHERE id = $npc->id";
		$db->setQuery($sql);
		$db->query();

		/////////////////////////////////////////////////////////////////////////////////////////////////
		$this->sendFeedback($player->iduser,$attack_message);

		$result[0]	= $player;
		$result[1]	= $npc;
		$result[2]	= $attack_message;
		return $result;
	}

	function dead_npc($npc)
	{
		$db		= JFactory::getDBO();
		$user		= JFactory::getUser();	
		$now		= time();
		$sql		= "UPDATE #__jigs_characters SET active = 0, empty = 1 , time_killed = $now WHERE id  = $npc->id";
		$db->setQuery($sql);
		$db->query();
		$sql		= "UPDATE #__jigs_inventory SET #__jigs_inventory.player_id = $user->id WHERE #__jigs_inventory.player_id = $npc->id";
		$db->setQuery($sql);
		$db->query();
		//// Update specific and General stats and payout when applicable

		$xp_type	= 'nbr_kills';
		$this->increment_xp($xp_type, $npc->money,$user->id);

		$text		= 'Citizen ' . $npc->name . ' was killed by citizen ' . $user->username . '<br/>' ;
		$this->sendWavyLines($text);
		$this->sendFeedback($user->id, $text);	
		return $text;
	}

	function dead_player($winner)
	{
		$user		= JFactory::getUser();
		$db		= JFactory::getDBO();		
		$now		= time();
		$db->setQuery("UPDATE #__jigs_players SET active = 3,  grid=1, map= 3, posx = 4, posy=5, empty= 1 , time_killed = " . $now . " 
			WHERE iduser ='".$user->id."'");
		$result		= $db->query();

		$db->setQuery("UPDATE #__jigs_inventory SET #__jigs_inventory.player_id = $winner WHERE #__jigs_inventory.player_id = " . $user->id );
		$result		= $db->query();

		$db->setQuery("UPDATE #__jigs_players SET money = 0 WHERE #__jigs_players.iduser = " .   $user->id ) ;
		$result		= $db->query();

		$text= 'Citizen ' .  $user->username  . ' was put in hospital by ' . $winner ;
		$this->sendWavyLines($text);
		return;
	}

	function increment_xp($xp_type ,$payment,$user_id)
	{
		$db 	= JFactory::getDBO();
		$query	="UPDATE #__jigs_players SET $xp_type  = $xp_type  +1, xp = xp+1, money = money + " . $payment .
			" WHERE #__jigs_players.iduser = " .  $user_id;
		$db->setQuery($query);
		$db->query();
		$this->test_level($user_id);
		return $query;
	}

	function test_level($user_id)
	{
		$user		= JFactory::getUser();
		$db		= JFactory::getDBO();
		$now		= time();
		$query		= "SELECT xp FROM #__jigs_players where iduser = $user_id";
		$db->setQuery($query);
		$xp		= $db->loadResult();
		$milestones	= array(100,200,400,800,1600,2000,4000,8000);

		foreach ($milestones as $check)
		{
			if ($xp == $check)
			{
				$query	= "UPDATE #__jigs_players SET level=level+1, statpoints = statpoints + 5 WHERE iduser = $user_id";
				$db->setQuery($query);
				$db->query();
				$text	= 'Citizen ' . $user->username . ' leveled up';
				$this->sendWavyLines($text);
				$this->sendFeedback($user->id,$text);
			}
		}
	}

	function swap()
	{
		$db		= JFactory::getDBO();
		$user		= JFactory::getUser();
		$weapon_id	= JRequest::getvar(weapon_id);
		$db->setQuery("UPDATE #__jigs_players SET id_weapon = '" . $weapon_id . "' WHERE iduser =".$user->id);
		$db->query();
		$result		= $weapon_id ;
		return $result;
	}

	

	function mine()
	{
		$db		= JFactory::getDBO();
		$user		= JFactory::getUser();
		$type		= JRequest::getvar(type);
		$building_id	= JRequest::getvar(building_id);
		$now		= time();
		$query		= "INSERT INTO #__jigs_mines (building, type, timestamp ) values  ($building_id,$type,$now) ON DUPLICATE KEY	UPDATE type =  $type , timestamp = " . $now;
		$db->setQuery($query);
		$db->query();
		$result[0]	= $type;
		//$result[1]	= $now;
		$result[1]	= $query;
		return $result;
	}

	function work_turbine()
	{
		$building_id	= JRequest::getvar(building_id);
		$line		= JRequest::getvar(line);
		$type		= JRequest::getvar(type);
		$quantity	= JRequest::getvar(quantity);
		$model		= $this->getModel('building');
		$result		= $model->work_turbine($building_id,$line,$type,$quantity);
		return $result;
	}

	function deposit()
	{
		$db		= JFactory::getDBO();
		$user		= JFactory::getUser();
		$qty		= JRequest::getvar(amount);
		$building_id	= JRequest::getvar(building_id);
		$now		= time();
		$db->setQuery("Select money, bank FROM #__jigs_players WHERE iduser = " . $user->id);
		$result		= $db->loadRow();
		$money		= $result[0];
		$bank		= $result[1];
		if ($qty <= $money)
		{
			$money = $money - $qty;
			$bank = $bank + $qty;
			$query = "UPDATE #__jigs_players SET money = $money, bank = $bank  WHERE iduser = " . $user->id;
			$db->setQuery($query);
			$db->query();
		}
		return $result;
	}

	function withdraw()
	{
		$db		= JFactory::getDBO();
		$user		= JFactory::getUser();
		$qty		= JRequest::getvar(amount);
		$building_id	= JRequest::getvar(building_id);
		$now		= time();
		$db->setQuery("Select money, bank FROM #__jigs_players WHERE iduser = ".$user->id);
		$result		= $db->loadRow();
		$money		= $result[0];
		$bank		= $result[1];

		if ($qty <= $bank){
			$money	= $money + $qty;
			$bank	= $bank - $qty;
			$query	= "UPDATE #__jigs_players SET money = $money, bank = $bank  WHERE iduser =" . $user->id;
			$db->setQuery($query);
			$db->query();
		}
		return $result;
	}

	function sell_crops()
	{
		$total_crops	= $this->get_total_crops();
		$payment	= $total_crops * 1000 ;
		$db		= JFactory::getDBO();
		$user		= JFactory::getUser();
		$query_1	= "SELECT money FROM #__jigs_players WHERE iduser = ' . $user->id . '";
		$db->setQuery($query_1);
		$money_saved	= $db->loadResult();
		$xp_type	= 'nbr_crops';
		$test		= $this->increment_xp($xp_type ,$payment,$user->id);
		$text		= $user->username . " has sold " . $total_crops . " crops.";
		$this->sendWavyLines($text);
		$query_2	= "Update #__jigs_farms LEFT JOIN #__jigs_buildings on #__jigs_farms.building = #__jigs_buildings.id SET total = 0 WHERE #__jigs_buildings.owner = $user->id";
		$db->setQuery($query_2);
		$db->query();

		$text		= "You sold " . $total_crops . " crops.";
		$this->sendFeedback($user->id, $text);
		return($test);
	}

	function get_total_crops()
	{
		$total_crop 	= 0;
		$db		= JFactory::getDBO();
		$user		= JFactory::getUser();
		$query		= "SELECT total FROM #__jigs_farms 
			LEFT JOIN #__jigs_buildings 
			ON #__jigs_farms.building = #__jigs_buildings.id 
			WHERE #__jigs_buildings.owner = $user->id; ";
		$db->setQuery($query);
		$result		= $db->loadResultArray();
		foreach($result as $row)
		{
			$total_crop = $total_crop + $row;
		}
		return ($total_crop);
	}

	function sendWavyLines($text)
	{
		jimport( 'joomla.application.component.helper' );
		jimport( 'joomla.html.parameter' );

		$component	= JComponentHelper::getComponent( 'com_battle' );
		$params		= new JParameter( $component->params );
		$sbid		= $params->get( 'shoutbox_category' );

		$db		= JFactory::getDBO();
		$now		= time();
		$sql		= "INSERT INTO #__shoutbox (name, time,sbid, text) VALUES ('Wavy Lines:', $now,$sbid, '$text' )";
		$db->setQuery($sql);
		$db->query();
		return $sql;
	}


	function sendFeedback($id,$text)
	{
		$db		= JFactory::getDBO();
		$query		= "INSERT INTO #__jigs_logs (user_id, message) VALUES ($id,'$text')";
		$db->setQuery($query) ;
		$db->query();
		return ;
	}

	function check_mine()
	{
		$now			= time();
		$building_id		= JRequest::getvar('building_id');
		$user			= JFactory::getUser();
		$db			= JFactory::getDBO();
		$query			="SELECT timestamp FROM #__jigs_mines WHERE building = $building_id";
		$db->setQuery($query);
		$result['timestamp']	= $db->loadResult();
		$result['now']		= date('l jS \of F Y h:i:s A',$now);
		$result['since']	= date('l jS \of F Y h:i:s A',$result['timestamp']);
		$result['elapsed']	= (int)(($now-$result['timestamp']));
		$result['remaining']	= (int)(50-((($now-$result['timestamp']))));
		return $result;
	}

	function check_farm()
	{
		$building_id		= JRequest::getvar('building');
		$field_id		= JRequest::getvar('field');
		//$user =& JFactory::getUser();
		$now			= time();
		$db			= JFactory::getDBO();
		$query			= "SELECT status,timestamp,finished 
			FROM #__jigs_farms 
			WHERE building = $building_id 
			AND field = $field_id";
		$db->setQuery($query);
		$result			= $db->loadAssoc();
		$result['now']		= date('l jS \of F Y h:i:s A',$now);
		$result['since']	= date('l jS \of F Y h:i:s A',$result['timestamp']);
		$result['elapsed']	= (int)(($now-$result['timestamp']));
		$result['remaining']	= (int)($result['finished'] - $now );
		$result['status']	= (int)($result['status']);
		$result['field']	= $field_id;

		return $result;
	}

	function check_reprocessor($building_id,$line_id)
	{
		$building_id		= JRequest::getvar('building');
		$line_id		= JRequest::getvar('line');
		//$user =& JFactory::getUser();
		$now			= time();
		$db			= JFactory::getDBO();
		$query			= "SELECT timestamp,finished FROM #__jigs_reprocessors WHERE building = $building_id AND line = $line_id";
		$db->setQuery($query);
		$result			= $db->loadAssoc();
		$result['now']		= date('l jS \of F Y h:i:s A',$now);
		$result['since']	= date('l jS \of F Y h:i:s A',$result['timestamp']);
		$result['elapsed']	= (int)(($now-$result['timestamp']));
		$result['remaining']= (int)($result['finished'] - $now );
		return $result;
	}

	function check_factory($building_id,$line_id)
	{
		$building_id		= JRequest::getvar('building');
		$line_id		= JRequest::getvar('line');
		//$user =& JFactory::getUser();
		$now			= time();
		$db			= JFactory::getDBO();
		$query			= "SELECT timestamp,finished FROM #__jigs_factories WHERE building = $building_id AND line = $line_id";
		$db->setQuery($query);
		$result			= $db->loadAssoc();
		$result['now']		= date('l jS \of F Y h:i:s A',$now);
		$result['since']	= date('l jS \of F Y h:i:s A',$result['timestamp']);
		$result['elapsed']	= (int)(($now-$result['timestamp']));
		$result['remaining']= (int)($result['finished'] - $now );
		return $result;
	}
	function get_battery()
	{
		$db			= JFactory::getDBO();
		$building_id		= JRequest::getvar('building_id');
		$battery_id		= JRequest::getvar('item');
		$user			= JFactory::getUser();
		$query			= "Update #__jigs_batteries SET iduser = $user->id  WHERE #__jigs_batteries.id = $battery_id";
		$db->setQuery($query);
		$db->query();
		return $battery_id;
	}


	function put_battery()
	{
		$db			= JFactory::getDBO();
		$building_id		= JRequest::getvar('building_id');
		$battery_id		= JRequest::getvar('item');
		$query			= "Update #__jigs_batteries SET iduser = $building_id WHERE #__jigs_batteries.id = $battery_id";
		$db->setQuery($query);
		$db->query();
		return $battery_id;
	}
}