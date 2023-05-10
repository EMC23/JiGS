"use strict";
/**
 * ---------------------------
 * Phaser + Colyseus - Part 1.
 * ---------------------------
 * - Connecting with the room
 * - Sending inputs at the user's framerate
 * - Update each player's positions WITHOUT interpolation
 */
var __importDefault = (this && this.__importDefault) || function (mod) {
    return (mod && mod.__esModule) ? mod : { "default": mod };
};
Object.defineProperty(exports, "__esModule", { value: true });
exports.PlayScene = void 0;
const phaser_1 = __importDefault(require("phaser"));
const colyseus_js_1 = require("colyseus.js");
const backend_1 = require("../backend");
class PlayScene extends phaser_1.default.Scene {
    constructor() {
        super({ key: "part1" });
        this.playerEntities = {};
        this.inputPayload = {
            left: false,
            right: false,
            up: false,
            down: false,
        };
    }
    async create() {
        this.cursorKeys = this.input.keyboard.createCursorKeys();
        this.debugFPS = this.add.text(4, 4, "", { color: "#ff0000", });
        // connect with the room
        await this.connect();
        this.room.state.players.onAdd((player, sessionId) => {
            const entity = this.physics.add.image(player.x, player.y, 'ship_0001');
            this.playerEntities[sessionId] = entity;
            // listening for server updates
            player.onChange(() => {
                //
                // update local position immediately
                // (WE WILL CHANGE THIS ON PART 2)
                //
                entity.x = player.x;
                entity.y = player.y;
            });
        });
        // remove local reference when entity is removed from the server
        this.room.state.players.onRemove((player, sessionId) => {
            const entity = this.playerEntities[sessionId];
            if (entity) {
                entity.destroy();
                delete this.playerEntities[sessionId];
            }
        });
        // this.cameras.main.startFollow(this.ship, true, 0.2, 0.2);
        // this.cameras.main.setZoom(1);
        this.cameras.main.setBounds(0, 0, 800, 600);
    }
    async connect() {
        // add connection status text
        const connectionStatusText = this.add
            .text(0, 0, "Trying to connect with the server...")
            .setStyle({ color: "#ff0000" })
            .setPadding(4);
        const client = new colyseus_js_1.Client(backend_1.BACKEND_URL);
        try {
            this.room = await client.joinOrCreate("part1_room", {});
            // connection successful!
            connectionStatusText.destroy();
        }
        catch (e) {
            // couldn't connect
            connectionStatusText.text = "Could not connect with the server.";
        }
    }
    update(time, delta) {
        // skip loop if not connected with room yet.
        if (!this.room) {
            return;
        }
        // send input to the server
        this.inputPayload.left = this.cursorKeys.left.isDown;
        this.inputPayload.right = this.cursorKeys.right.isDown;
        this.inputPayload.up = this.cursorKeys.up.isDown;
        this.inputPayload.down = this.cursorKeys.down.isDown;
        this.room.send(0, this.inputPayload);
        this.debugFPS.text = `Frame rate: ${this.game.loop.actualFps}`;
    }
}
exports.PlayScene = PlayScene;