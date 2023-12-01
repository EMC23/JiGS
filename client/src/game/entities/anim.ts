/**
 * -------Anim ---------
 */

import Phaser from 'phaser'

const createCharacterAnims = (anims: Phaser.Animations.AnimationManager, entity,tag) => {

    if (tag == 'default') {

        //console.log('adding entity in default:' + entity);

        anims.create({
            key: 'walkLeft_' + entity,
            frames: anims.generateFrameNumbers(entity, { frames: [117, 118, 119, 120, 121, 122, 123, 124, 125] }),
            frameRate: 12,
            repeat: -1
        });

        anims.create({
            key: 'walkRight_' + entity,
            frames: anims.generateFrameNumbers(entity, { frames: [143, 144, 145, 146, 147, 148, 149, 150, 151] }),
            frameRate: 12,
            repeat: -1
        });

        anims.create({
            key: 'walkUp_' + entity,
            frames: anims.generateFrameNumbers(entity, { frames: [104, 105, 106, 107, 108, 109, 110, 110, 112] }),
            frameRate: 12,
            repeat: -1
        });

        anims.create({
            key: 'walkDown_' + entity,
            frames: anims.generateFrameNumbers(entity, { frames: [130, 131, 132, 133, 134, 135, 136, 137, 138] }),
            frameRate: 12,
            repeat: -1
        });

        anims.create({
            key: 'stop_' + entity,
            frames: anims.generateFrameNumbers(entity, { frames: [130, 130, 130, 130, 130, 130, 130, 130, 138] }),
            frameRate: 12,
            repeat: -1
        });

     /*     anims.create({
            key: 'thrustLeft_' + entity,
            frames: anims.generateFrameNumbers(entity, { frames: [117, 118, 119, 120, 121, 122, 123, 124, 125] }),
            frameRate: 12,
            repeat: -1
        });

        anims.create({
            key: 'thrustRight_' + entity,
            frames: anims.generateFrameNumbers(entity, { frames: [143, 144, 145, 146, 147, 148, 149, 150, 151] }),
            frameRate: 12,
            repeat: -1
        });

        anims.create({
            key: 'thrustUp_' + entity,
            frames: anims.generateFrameNumbers(entity, { frames: [14, 15, 16, 17, 18, 19, 10, 11, 12] }),
            frameRate: 12,
            repeat: -1
        });

        anims.create({
            key: 'thrustDown_' + entity,
            frames: anims.generateFrameNumbers(entity, { frames: [130, 131, 132, 133, 134, 135, 136, 137, 138] }),
            frameRate: 12,
            repeat: -1
        }); */

        anims.create({
            key: 'hurt_' + entity,
            frames: anims.generateFrameNumbers(entity, { frames: [260, 261, 262, 263, 264, 265] }),
            frameRate: 12,
            repeat: 0
        });

/*         anims.create({
            key: 'kick_' + entity,
            frames: anims.generateFrameNumbers(entity, { frames: [10, 11, 12, 13, 10] }),
            frameRate: 12,
            repeat: -1,
            repeatDelay: 2000
        }); */
    }

    if (tag == 'slash_oversize') {

       // console.log('adding entity in slash_oversize:' + entity);

       anims.create({
            key: 'walkLeft_' + entity,
            frames: anims.generateFrameNumbers(entity, { frames: [162, 163, 164, 165, 166, 166, 167, 168, 169] }),
            frameRate: 12,
            repeat: -1
        });

        anims.create({
            key: 'walkUp_' + entity,
            frames: anims.generateFrameNumbers(entity, { frames: [144, 145, 146, 147, 148,149,150,151,152] }),
            frameRate: 12,
            repeat: -1
        });

        anims.create({
            key: 'walkRight_' + entity,
            frames: anims.generateFrameNumbers(entity, { frames: [198, 199, 200, 201, 202, 203, 204, 205, 206] }),
            frameRate: 12,
            repeat: -1
        });

        anims.create({
            key: 'walkDown_' + entity,
            frames: anims.generateFrameNumbers(entity, { frames: [ 180, 181, 182, 183, 184, 185, 186,187,188] }),
            frameRate: 12,
            repeat: -1
        });

         anims.create({
            key: 'stop_' + entity,
            frames: anims.generateFrameNumbers(entity, { frames: [180, 180, 180, 180, 180, 180, 180, 180, 180] }),
            frameRate: 12,
            repeat: -1
        });

        anims.create({
            key: 'thrustUp_' + entity,
            frames: anims.generateFrameNumbers(entity, { frames: [43, 44, 45, 48, 46, 47,43] }),
            frameRate: 12,
            repeat: 0
        });

        anims.create({
            key: 'thrustLeft_' + entity,
            frames: anims.generateFrameNumbers(entity, { frames: [ 48, 49, 50, 51, 52, 53,48] }),
            frameRate: 12,
            repeat: 0
        });

        anims.create({
            key: 'thrustDown_' + entity,
            frames: anims.generateFrameNumbers(entity, { frames: [54, 55, 56, 57, 58, 59,54] }),
            frameRate: 12,
            repeat: 0
        });

        anims.create({
            key: 'thrustRight_' + entity,
            frames: anims.generateFrameNumbers(entity, { frames: [60, 61, 62, 63, 64, 65,60] }),
            frameRate: 12,
            repeat: 0
        });

 /*        anims.create({
            key: 'thrustLeft_' + entity ,
            frames: anims.generateFrameNumbers(entity, { frames: [388, 389, 390, 391, 392, 393] }),
            frameRate: 12,
            repeat: -1
        });

        anims.create({
            key: 'thrustDown_' + entity ,
            frames: anims.generateFrameNumbers(entity, { frames: [394, 395, 396, 397, 398, 399] }),
            frameRate: 12,
            repeat: -1
        });

        anims.create({
            key: 'thrustRight_' + entity ,
            frames: anims.generateFrameNumbers(entity, { frames: [402, 403, 404, 405, 406, 406] }),
            frameRate: 12,
            repeat: -1
        }); */

          anims.create({
            key: 'hurt_' + entity,
            frames: anims.generateFrameNumbers(entity, { frames: [360, 361, 362, 363, 364, 365] }),
            frameRate: 12,
            repeat: 0
        });

/*         anims.create({
            key: 'kick_' + entity,
            frames: anims.generateFrameNumbers(entity, { frames: [10, 11, 12, 13, 10] }),
            frameRate: 12,
            repeat: -1,
            repeatDelay: 2000
        }); */
    }
}

export {
    createCharacterAnims
}