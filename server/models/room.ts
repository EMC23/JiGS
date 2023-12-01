var mysql = require("mysql2");
import config from '../services/db';

var con = mysql.createConnection({
  host: config.host,
  user: config.user,
  password: config.password,
  database: config.database
});


function getRoom(id) {
  return new Promise(function (resolve, reject) {
    con.connect(function (err) {
      if (err) throw err;
      con.query(
        `SELECT
        node__field_map_width.field_map_width_value,
        node__field_map_height.field_map_height_value
        FROM node__field_map_width
        LEFT JOIN node__field_map_height
        ON node__field_map_width.entity_id = node__field_map_height.entity_id
        WHERE node__field_map_width.entity_id = ` + id,
        function (err, result) {
          if (err) throw err;
          resolve(result);
        }
      );
    });
  });
}

function getPortals(NodeNumber) {
  return new Promise(function (resolve, reject) {
    con.connect(function (err) {
      if (err) throw err;
      con.query(
        `SELECT node__field_portals.field_portals_target_id,
     paragraph__field_destination.field_destination_target_id,
     paragraph__field_tiled.field_tiled_value,
     paragraph__field_destination_x.field_destination_x_value,
     paragraph__field_destination_y.field_destination_y_value,
     paragraph__field_x.field_x_value,
     paragraph__field_y.field_y_value
     FROM node__field_portals Left
     Join paragraph__field_destination
      On node__field_portals.field_portals_target_id = paragraph__field_destination.entity_id
      Left Join paragraph__field_tiled
      On node__field_portals.field_portals_target_id = paragraph__field_tiled.entity_id
      Left Join paragraph__field_destination_x
      On node__field_portals.field_portals_target_id = paragraph__field_destination_x.entity_id
      Left Join paragraph__field_destination_y
      On node__field_portals.field_portals_target_id = paragraph__field_destination_y.entity_id
      Left Join paragraph__field_x
      On node__field_portals.field_portals_target_id = paragraph__field_x.entity_id
      Left Join paragraph__field_y
      On node__field_portals.field_portals_target_id = paragraph__field_y.entity_id
      where node__field_portals.entity_id = ` + NodeNumber,
        function (err, result) {
          if (err) throw err;
          resolve(result);
        }
      );
    });
  });
}

function getNpcs(NodeNumber) {
  return new Promise(function (resolve, reject) {
    con.connect(function (err) {
      if (err) throw err;
      con.query(
        `SELECT
     node__field_npc.field_npc_target_id,
     paragraph__field_name.field_name_target_id,
     node_field_data.title,
     paragraph__field_x.field_x_value ,
     paragraph__field_y.field_y_value
     FROM node__field_npc Left Join paragraph__field_name
     On node__field_npc.field_npc_target_id =paragraph__field_name.entity_id
     Left Join node_field_data
     On node_field_data.nid = paragraph__field_name.field_name_target_id
     Left Join paragraph__field_x
     On paragraph__field_x.entity_id = paragraph__field_name.entity_id
     Left Join paragraph__field_y
     On paragraph__field_y.entity_id = paragraph__field_name.entity_id
     WHERE node__field_npc.entity_id = ` + NodeNumber,
        function (err, result) {
          if (err) throw err;
          resolve(result);
        }
      );
    });
  });
}

function getMobs(NodeNumber) {
  return new Promise(function (resolve, reject) {
    con.connect(function (err) {
      if (err) throw err;
      con.query(
        `SELECT node__field_mobs.field_mobs_target_id,
       paragraph__field_mob_name.field_mob_name_value,
       paragraph__field_x.field_x_value,
       paragraph__field_y.field_y_value
       FROM node__field_mobs
       Left Join paragraph__field_mob_name
       On paragraph__field_mob_name.entity_id = node__field_mobs.field_mobs_target_id
       Left Join paragraph__field_x
       On paragraph__field_x.entity_id = node__field_mobs.field_mobs_target_id
       Left Join paragraph__field_y
       On paragraph__field_y.entity_id = node__field_mobs.field_mobs_target_id
       WHERE node__field_mobs.entity_id =
        ` + NodeNumber,
        function (err, result) {
          if (err) throw err;
          resolve(result);
        }
      );
    });
  });
}

function getRewards(NodeNumber) {
  return new Promise(function (resolve, reject) {
    con.connect(function (err) {
      if (err) throw err;
      con.query(
        `SELECT
     node__field_rewards.field_rewards_target_id,
     paragraph__field_x.field_x_value,
     paragraph__field_y.field_y_value,
     paragraph__field_ref.field_ref_value
     FROM node__field_rewards
     LEFT JOIN paragraph__field_x
     ON paragraph__field_x.entity_id = node__field_rewards.field_rewards_target_id
     LEFT JOIN paragraph__field_y
     ON paragraph__field_y.entity_id = node__field_rewards.field_rewards_target_id
     LEFT JOIN paragraph__field_ref
     ON paragraph__field_ref.entity_id = node__field_rewards.field_rewards_target_id

     WHERE node__field_rewards.entity_id = ` + NodeNumber,
        function (err, result) {
          if (err) throw err;
          resolve(result);
        }
      );
    });
  });
}

function updateBanks() {
  con.connect(function (err) {
    if (err) throw err;
    con.query(
      `UPDATE profile__field_credits SET field_credits_value = field_credits_value + 1 WHERE 1 = 1`,
      function (err, result, fields) {
        if (err) throw err;
        return true;
      }
    );
  });
  return;
}

module.exports = {
  getRoom,
  getPortals,
  getNpcs,
  getMobs,
  getRewards,
  updateBanks
};