//
//  Tile.m
//  MathApp
//
//  Created by Xiaoyun Yang on 7/17/14.
//  Copyright (c) 2014 Apportable. All rights reserved.
//

#import "ShapeTile.h"
#import "MainScene.h"

@implementation ShapeTile {
    CCSprite* _image;
    CCButton* _tileButton;
}

-(void) changeNodeImage :(NSNumber*)keyVal {
    //_image = [CCSprite spriteWithImageNamed:@"Shapes/3x3.png"];
    //imageName = @"Shapes/10x10.png";
    NSMutableString* imageName = [self getShapeImageNameFromKey:keyVal];
    //NSLog(@"imageName is........%@", imageName);
    _image.spriteFrame = [CCSpriteFrame frameWithImageNamed:imageName];
    _tileButton.selected = false;
}

/********************** selectTile ********************/
//called everytime the tile is clicked
-(void)selectTile {
    [MainScene tileTouched];
    //NSLog(@"AFTER CLICK: shape tile is selected: %d\n", _tileButton.selected);
}

/********************** getImageNameFromKey ********************/
-(NSMutableString*) getShapeImageNameFromKey :(NSNumber*)key {
    NSString* folderName = @"Shapes/";
    NSString* extension = @".png";
    NSMutableString* shapeName=[NSMutableString stringWithString:@""];
    
    int keyVal = [key intValue];
    
    //NSLog(@"keyVal is........%d", keyVal);
    
    switch(keyVal) {
        case 0:
            [shapeName setString:@"3x3"];
            break;
        case 1:
            [shapeName setString:@"3x4"];
            break;
        case 2:
            [shapeName setString:@"3x5"];
            break;
        case 3:
            [shapeName setString:@"3x6"];
            break;
        case 4:
            [shapeName setString:@"3x7"];
            break;
        case 5:
            [shapeName setString:@"3x8"];
            break;
        case 6:
            [shapeName setString:@"3x9"];
            break;
        case 7:
            [shapeName setString:@"3x10"];
            break;
        case 8:
            [shapeName setString:@"4x3"];
            break;
        case 9:
            [shapeName setString:@"4x4"];
            break;
        case 10:
            [shapeName setString:@"4x5"];
            break;
        case 11:
            [shapeName setString:@"4x6"];
            break;
        case 12:
            [shapeName setString:@"4x7"];
            break;
        case 13:
            [shapeName setString:@"4x8"];
            break;
        case 14:
            [shapeName setString:@"4x9"];
            break;
        case 15:
            [shapeName setString:@"4x10"];
            break;
        case 16:
            [shapeName setString:@"5x3"];
            break;
        case 17:
            [shapeName setString:@"5x4"];
            break;
        case 18:
            [shapeName setString:@"5x5"];
            break;
        case 19:
            [shapeName setString:@"5x6"];
            break;
        case 20:
            [shapeName setString:@"5x7"];
            break;
        case 21:
            [shapeName setString:@"5x8"];
            break;
        case 22:
            [shapeName setString:@"5x9"];
            break;
        case 23:
            [shapeName setString:@"5x10"];
            break;
        case 24:
            [shapeName setString:@"6x3"];
            break;
        case 25:
            [shapeName setString:@"6x4"];
            break;
        case 26:
            [shapeName setString:@"6x5"];
            break;
        case 27:
            [shapeName setString:@"6x6"];
            break;
        case 28:
            [shapeName setString:@"6x7"];
            break;
        case 29:
            [shapeName setString:@"6x8"];
            break;
        case 30:
            [shapeName setString:@"6x9"];
            break;
        case 31:
            [shapeName setString:@"6x10"];
            break;
        case 32:
            [shapeName setString:@"7x3"];
            break;
        case 33:
            [shapeName setString:@"7x4"];
            break;
        case 34:
            [shapeName setString:@"7x5"];
            break;
        case 35:
            [shapeName setString:@"7x6"];
            break;
        case 36:
            [shapeName setString:@"7x7"];
            break;
        case 37:
            [shapeName setString:@"7x8"];
            break;
        case 38:
            [shapeName setString:@"7x9"];
            break;
        case 39:
            [shapeName setString:@"7x10"];
            break;
        case 40:
            [shapeName setString:@"8x3"];
            break;
        case 41:
            [shapeName setString:@"8x4"];
            break;
        case 42:
            [shapeName setString:@"8x5"];
            break;
        case 43:
            [shapeName setString:@"8x6"];
            break;
        case 44:
            [shapeName setString:@"8x7"];
            break;
        case 45:
            [shapeName setString:@"8x8"];
            break;
        case 46:
            [shapeName setString:@"8x9"];
            break;
        case 47:
            [shapeName setString:@"8x10"];
            break;
        case 48:
            [shapeName setString:@"9x3"];
            break;
        case 49:
            [shapeName setString:@"9x4"];
            break;
        case 50:
            [shapeName setString:@"9x5"];
            break;
        case 51:
            [shapeName setString:@"9x6"];
            break;
        case 52:
            [shapeName setString:@"9x7"];
            break;
        case 53:
            [shapeName setString:@"9x8"];
            break;
        case 54:
            [shapeName setString:@"9x9"];
            break;
        case 55:
            [shapeName setString:@"9x10"];
            break;
        case 56:
            [shapeName setString:@"10x3"];
            break;
        case 57:
            [shapeName setString:@"10x4"];
            break;
        case 58:
            [shapeName setString:@"10x5"];
            break;
        case 59:
            [shapeName setString:@"10x6"];
            break;
        case 60:
            [shapeName setString:@"10x7"];
            break;
        case 61:
            [shapeName setString:@"10x8"];
            break;
        case 62:
            [shapeName setString:@"10x9"];
            break;
        case 63:
            [shapeName setString:@"10x10"];
            break;
    }
    //NSLog(@"after switch statement........shapeName is %@", shapeName);
    NSMutableString* path = [NSMutableString stringWithFormat:@"%@%@%@", folderName, shapeName, extension];
    return path;
}
/********************** getImageNameFromKey ********************/
-(NSMutableString*) getValueImageNameFromKey :(NSNumber*)key {
    NSString* folderName = @"Numbers/";
    NSString* extension = @".png";
    NSMutableString* valueName=[NSMutableString stringWithString:@""];
    
    int keyVal = [key intValue];
    
    //NSLog(@"keyVal is........%d", keyVal);
    
    switch(keyVal) {
        case 0:
            [valueName setString:@"9"];//3x3
            break;
        case 1:
            [valueName setString:@"12"];//3x4
            break;
        case 2:
            [valueName setString:@"15"];//3x5
            break;
        case 3:
            [valueName setString:@"18"];//3x6
            break;
        case 4:
            [valueName setString:@"21"];//3x7
            break;
        case 5:
            [valueName setString:@"24"];//3x8
            break;
        case 6:
            [valueName setString:@"27"];//3x9
            break;
        case 7:
            [valueName setString:@"30"];//3x10
            break;
        case 8:
            [valueName setString:@"12"];//4x3
            break;
        case 9:
            [valueName setString:@"16"];//4x4
            break;
        case 10:
            [valueName setString:@"20"];//4x5
            break;
        case 11:
            [valueName setString:@"24"];//4x6
            break;
        case 12:
            [valueName setString:@"28"];//4x7
            break;
        case 13:
            [valueName setString:@"32"];//4x8
            break;
        case 14:
            [valueName setString:@"36"];//4x9
            break;
        case 15:
            [valueName setString:@"40"];//4x10
            break;
        case 16:
            [valueName setString:@"15"];//5x3
            break;
        case 17:
            [valueName setString:@"20"];//5x4
            break;
        case 18:
            [valueName setString:@"25"];//5x5
            break;
        case 19:
            [valueName setString:@"30"];//5x6
            break;
        case 20:
            [valueName setString:@"35"];//5x7
            break;
        case 21:
            [valueName setString:@"40"];//5x8
            break;
        case 22:
            [valueName setString:@"45"];//5x9
            break;
        case 23:
            [valueName setString:@"50"];//5x10
            break;
        case 24:
            [valueName setString:@"18"];//6x3
            break;
        case 25:
            [valueName setString:@"24"];//6x4
            break;
        case 26:
            [valueName setString:@"30"];//6x5
            break;
        case 27:
            [valueName setString:@"36"];//6x6
            break;
        case 28:
            [valueName setString:@"42"];//6x7
            break;
        case 29:
            [valueName setString:@"48"];//6x8
            break;
        case 30:
            [valueName setString:@"54"];//6x9
            break;
        case 31:
            [valueName setString:@"60"];//6x10
            break;
        case 32:
            [valueName setString:@"21"];//7x3
            break;
        case 33:
            [valueName setString:@"28"];//7x4
            break;
        case 34:
            [valueName setString:@"35"];//7x5
            break;
        case 35:
            [valueName setString:@"42"];//7x6
            break;
        case 36:
            [valueName setString:@"49"];//7x7
            break;
        case 37:
            [valueName setString:@"56"];//7x8
            break;
        case 38:
            [valueName setString:@"63"];//7x9
            break;
        case 39:
            [valueName setString:@"70"];//7x10
            break;
        case 40:
            [valueName setString:@"24"];//8x3
            break;
        case 41:
            [valueName setString:@"32"];//8x4
            break;
        case 42:
            [valueName setString:@"40"];//8x5
            break;
        case 43:
            [valueName setString:@"48"];//8x6
            break;
        case 44:
            [valueName setString:@"56"];//8x7
            break;
        case 45:
            [valueName setString:@"64"];//8x8
            break;
        case 46:
            [valueName setString:@"72"];//8x9
            break;
        case 47:
            [valueName setString:@"80"];//8x10
            break;
        case 48:
            [valueName setString:@"27"];//9x3
            break;
        case 49:
            [valueName setString:@"36"];//9x4
            break;
        case 50:
            [valueName setString:@"45"];//9x5
            break;
        case 51:
            [valueName setString:@"54"];//9x6
            break;
        case 52:
            [valueName setString:@"63"];//9x7
            break;
        case 53:
            [valueName setString:@"72"];//9x8
            break;
        case 54:
            [valueName setString:@"81"];//9x9
            break;
        case 55:
            [valueName setString:@"90"];//9x10
            break;
        case 56:
            [valueName setString:@"30"];//10x3
            break;
        case 57:
            [valueName setString:@"40"];//10x4
            break;
        case 58:
            [valueName setString:@"50"];//10x5
            break;
        case 59:
            [valueName setString:@"60"];//10x6
            break;
        case 60:
            [valueName setString:@"70"];//10x7
            break;
        case 61:
            [valueName setString:@"80"];//10x8
            break;
        case 62:
            [valueName setString:@"90"];//10x9
            break;
        case 63:
            [valueName setString:@"100"];//10x10
            break;
    }
    //NSLog(@"after switch statement........shapeName is %@", shapeName);
    NSMutableString* path = [NSMutableString stringWithFormat:@"%@%@%@", folderName, valueName, extension];
    return path;
}

@end
