//
//  Tile.h
//  MathApp
//
//  Created by Xiaoyun Yang on 7/17/14.
//  Copyright (c) 2014 Apportable. All rights reserved.
//

#import "CCNode.h"

@interface ShapeTile : CCNode
//@property (nonatomic, assign) CCSprite* image;


-(NSMutableString*)getImageNameFromKey:(NSNumber*)key;
-(void) changeNodeImage :(NSNumber*)keyVal;

@end
