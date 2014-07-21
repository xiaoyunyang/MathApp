//
//  EquationTile.h
//  MathApp
//
//  Created by Xiaoyun Yang on 7/18/14.
//  Copyright (c) 2014 Apportable. All rights reserved.
//

#import "CCNode.h"
#import "MainScene.h"

@interface EquationTile : CCNode
-(NSMutableString*)getEquationImageNameFromKey:(NSNumber*)key;
-(void) changeNodeImage :(NSNumber*)keyVal;
@end
