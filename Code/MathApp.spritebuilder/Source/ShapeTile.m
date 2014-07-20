//
//  Tile.m
//  MathApp
//
//  Created by Xiaoyun Yang on 7/17/14.
//  Copyright (c) 2014 Apportable. All rights reserved.
//

#import "ShapeTile.h"

@implementation ShapeTile {
    CCSprite *_image;
}

-(void) changeNodeImage :(NSNumber*)keyVal {
    //_image = [CCSprite spriteWithImageNamed:@"Shapes/3x3.png"];
    //imageName = @"Shapes/10x10.png";
    NSMutableString* imageName = [self getImageNameFromKey:keyVal];
    //NSLog(@"imageName is........%@", imageName);
    _image.spriteFrame = [CCSpriteFrame frameWithImageNamed:imageName];
}


/********************** getImageNameFromKey ********************/
-(NSMutableString*) getImageNameFromKey :(NSNumber*)key {
    NSString* folderName = @"Shapes/";
    NSString* extension = @".png";
    NSMutableString* shapeName=[NSMutableString stringWithString:@""];
    
    int keyVal = [key intValue];
    
    NSLog(@"keyVal is........%d", keyVal);
    
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


@end
