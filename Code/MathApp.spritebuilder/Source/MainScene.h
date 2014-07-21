//
//  MainScene.h
//  PROJECTNAME
//
//  Created by Viktor on 10/10/13.
//  Copyright (c) 2013 Apportable. All rights reserved.
//

#import <Foundation/Foundation.h>
#include <stdlib.h>
#import "CCNode.h"

@interface MainScene : CCNode {
    NSMutableArray *_shapeArray; //size 8 matrix
    NSMutableArray *_equationArray; //size 7 matrix
}

+(void)tileTouched;

@property(nonatomic, copy, readwrite) NSMutableArray* _shapeArray;
@property(nonatomic, copy, readwrite) NSMutableArray* _equationArray;

@end
