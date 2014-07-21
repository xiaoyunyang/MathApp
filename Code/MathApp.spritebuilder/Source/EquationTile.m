//
//  EquationTile.m
//  MathApp
//
//  Created by Xiaoyun Yang on 7/18/14.
//  Copyright (c) 2014 Apportable. All rights reserved.
//

#import "EquationTile.h"
#import "MainScene.h"

@implementation EquationTile {
    CCSprite* _image;
    CCButton* _tileButton;
}

-(void) changeNodeImage :(NSNumber*)keyVal {
    NSMutableString* imageName = [self getEquationImageNameFromKey:keyVal];
    _image.spriteFrame = [CCSpriteFrame frameWithImageNamed:imageName];
    _tileButton.selected = false;
}

/********************** selectTile ********************/
-(void)selectTile {
    [MainScene tileTouched];
    //NSLog(@"equation tile is selected!");
}


/********************** getImageNameFromKey ********************/
-(NSMutableString*) getEquationImageNameFromKey :(NSNumber*)key {
    NSString* folderName = @"Equations/";
    NSString* extension = @"_Eq.png";
    NSMutableString* equationName=[NSMutableString stringWithString:@""];
    
    int keyVal = [key intValue];
    
    //NSLog(@"keyVal is........%d", keyVal);
    
    switch(keyVal) {
        case 0:
            [equationName setString:@"3x3"];
            break;
        case 1:
            [equationName setString:@"3x4"];
            break;
        case 2:
            [equationName setString:@"3x5"];
            break;
        case 3:
            [equationName setString:@"3x6"];
            break;
        case 4:
            [equationName setString:@"3x7"];
            break;
        case 5:
            [equationName setString:@"3x8"];
            break;
        case 6:
            [equationName setString:@"3x9"];
            break;
        case 7:
            [equationName setString:@"3x10"];
            break;
        case 8:
            [equationName setString:@"4x3"];
            break;
        case 9:
            [equationName setString:@"4x4"];
            break;
        case 10:
            [equationName setString:@"4x5"];
            break;
        case 11:
            [equationName setString:@"4x6"];
            break;
        case 12:
            [equationName setString:@"4x7"];
            break;
        case 13:
            [equationName setString:@"4x8"];
            break;
        case 14:
            [equationName setString:@"4x9"];
            break;
        case 15:
            [equationName setString:@"4x10"];
            break;
        case 16:
            [equationName setString:@"5x3"];
            break;
        case 17:
            [equationName setString:@"5x4"];
            break;
        case 18:
            [equationName setString:@"5x5"];
            break;
        case 19:
            [equationName setString:@"5x6"];
            break;
        case 20:
            [equationName setString:@"5x7"];
            break;
        case 21:
            [equationName setString:@"5x8"];
            break;
        case 22:
            [equationName setString:@"5x9"];
            break;
        case 23:
            [equationName setString:@"5x10"];
            break;
        case 24:
            [equationName setString:@"6x3"];
            break;
        case 25:
            [equationName setString:@"6x4"];
            break;
        case 26:
            [equationName setString:@"6x5"];
            break;
        case 27:
            [equationName setString:@"6x6"];
            break;
        case 28:
            [equationName setString:@"6x7"];
            break;
        case 29:
            [equationName setString:@"6x8"];
            break;
        case 30:
            [equationName setString:@"6x9"];
            break;
        case 31:
            [equationName setString:@"6x10"];
            break;
        case 32:
            [equationName setString:@"7x3"];
            break;
        case 33:
            [equationName setString:@"7x4"];
            break;
        case 34:
            [equationName setString:@"7x5"];
            break;
        case 35:
            [equationName setString:@"7x6"];
            break;
        case 36:
            [equationName setString:@"7x7"];
            break;
        case 37:
            [equationName setString:@"7x8"];
            break;
        case 38:
            [equationName setString:@"7x9"];
            break;
        case 39:
            [equationName setString:@"7x10"];
            break;
        case 40:
            [equationName setString:@"8x3"];
            break;
        case 41:
            [equationName setString:@"8x4"];
            break;
        case 42:
            [equationName setString:@"8x5"];
            break;
        case 43:
            [equationName setString:@"8x6"];
            break;
        case 44:
            [equationName setString:@"8x7"];
            break;
        case 45:
            [equationName setString:@"8x8"];
            break;
        case 46:
            [equationName setString:@"8x9"];
            break;
        case 47:
            [equationName setString:@"8x10"];
            break;
        case 48:
            [equationName setString:@"9x3"];
            break;
        case 49:
            [equationName setString:@"9x4"];
            break;
        case 50:
            [equationName setString:@"9x5"];
            break;
        case 51:
            [equationName setString:@"9x6"];
            break;
        case 52:
            [equationName setString:@"9x7"];
            break;
        case 53:
            [equationName setString:@"9x8"];
            break;
        case 54:
            [equationName setString:@"9x9"];
            break;
        case 55:
            [equationName setString:@"9x10"];
            break;
        case 56:
            [equationName setString:@"10x3"];
            break;
        case 57:
            [equationName setString:@"10x4"];
            break;
        case 58:
            [equationName setString:@"10x5"];
            break;
        case 59:
            [equationName setString:@"10x6"];
            break;
        case 60:
            [equationName setString:@"10x7"];
            break;
        case 61:
            [equationName setString:@"10x8"];
            break;
        case 62:
            [equationName setString:@"10x9"];
            break;
        case 63:
            [equationName setString:@"10x10"];
            break;
    }
    //NSLog(@"after switch statement........equationName is %@", equationName);
    NSMutableString* path = [NSMutableString stringWithFormat:@"%@%@%@", folderName, equationName, extension];
    return path;
}

@end
