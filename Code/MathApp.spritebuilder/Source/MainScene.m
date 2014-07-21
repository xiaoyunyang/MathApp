//
//  MainScene.m
//  MATHAPP
//
//  Created by Xiaoyun on 7/3/14.
//  Copyright (c) 2014 Apportable. All rights reserved.
//

#import "MainScene.h"
#import "Grid.h"
@implementation MainScene {
    Grid* _grid;
    
    CCButton* _descButton;
    CCSprite* _descExpression;
    CCSprite* _descEqualSign;
    CCSprite* _descValue;
    
    CCLabelTTF *_score;
    CCLabelTTF *_timer;
    
}
@synthesize _shapeArray = _shapeArray;
@synthesize _equationArray = _equationArray;

static const NSInteger SHAPE_TILES = 8;
static const NSInteger EQUATION_TILES = 7;
static const NSInteger TOTAL_MATCHES = 64; //64 total equations/tiles

-(void)didLoadFromCCB {
    // tell this scene to accept touches
    self.userInteractionEnabled = true;
    
    //initialize descriptor parameters with everything cleared
    [self clearDescriptor];
    
    

    [self createMatches];
    [_grid refreshGameboard:_shapeArray :_equationArray];
}

-(void)clearDescriptor {
    _descButton.selected = false;
    _descExpression.spriteFrame = NULL;
    _descEqualSign.spriteFrame = NULL;
    _descValue.spriteFrame = NULL;
}

-(void)showMatchInDescriptor {
    _descButton.selected = true;
    //_descExpression.spriteFrame = [CCSpriteFrame frameWithImageNamed:@""];
}

/************************ tileTouched ******************/
// called by ShapeTile or EquationTile whenever any tile is touched
+(void)tileTouched {
     NSLog(@"a tile is touched. querying every tile for selection status...");
}
/********************** selectDescriptor ********************/
//Descriptor is clicked
-(void)selectDescriptor {
    NSLog(@"descriptor is clicked!");
}


/************************ createMatches ******************/
//creates 2 arrays of random equations and equivalent shape key values. Key values will be
//provided to ShapeTile and EquationTile to render images that correspond with the key values
-(void)createMatches {
    //initialize _shapeArray and _equationArray as a bunch of -1
    _shapeArray = [NSMutableArray array];
    _equationArray = [NSMutableArray array];
    for (int i = 0; i< EQUATION_TILES; i++) {
        [_shapeArray addObject:[NSNumber numberWithInt:-1]];
        [_equationArray addObject:[NSNumber numberWithInt:-1]];
    }
    [_shapeArray addObject:@(-1)];
    //NSLog(@"shape Array: %@\n", _shapeArray);
    //NSLog(@"equation Array: %@\n", _equationArray);

    //invoke random number generator to pick 8 random numbers from 0 to TOTAL_MATCHES-1;
    NSNumber* randomShapeKey;
    for(int i=0; i<EQUATION_TILES; i++) {
        randomShapeKey = [NSNumber numberWithInt:(arc4random()%TOTAL_MATCHES)];
        //if _shapeArray already contains the random number, keep generating random
        while([_shapeArray indexOfObject:randomShapeKey] != NSNotFound) {
            randomShapeKey = [NSNumber numberWithInt:(arc4random()%TOTAL_MATCHES)];
        }
        //break out of while loop once a random number that is not already in _shapeArray is generated
        [_shapeArray replaceObjectAtIndex:i withObject:randomShapeKey];
        [_equationArray replaceObjectAtIndex:i withObject:randomShapeKey];
    }
    //add one more to _shapeArray
    while([_shapeArray indexOfObject:randomShapeKey] != NSNotFound) {
        randomShapeKey = [NSNumber numberWithInt:(arc4random()%TOTAL_MATCHES)];
    }
    [_shapeArray replaceObjectAtIndex:(SHAPE_TILES-1) withObject:randomShapeKey];
    
    //NSLog(@"shape Array: %@\n", _shapeArray);
    //NSLog(@"equation Array: %@\n", _equationArray);
    
    //NSLog(@"shuffling both arrays....");
    //knuth shuffle _shapeArray
    for (int i=SHAPE_TILES-1; i>0; i--) {
        int j = arc4random_uniform(i+1); //randome number 0...i
        [_shapeArray exchangeObjectAtIndex:i withObjectAtIndex:j];
    }

    //knuth shuffle _equationArray
    for (int i=EQUATION_TILES-1; i>0; i--) {
        int j = arc4random_uniform(i+1); //randome number 0...i
        [_equationArray exchangeObjectAtIndex:i withObjectAtIndex:j];
    }
    
    //NSLog(@"shape Array: %@\n", _shapeArray);
    //NSLog(@"equation Array: %@\n", _equationArray);
    
    /****************** testing ****************/
    //remove below code after testing clicking functionality
    [_shapeArray replaceObjectAtIndex:0 withObject:[NSNumber numberWithInt:23]];
    [_shapeArray replaceObjectAtIndex:1 withObject:[NSNumber numberWithInt:52]];
    [_shapeArray replaceObjectAtIndex:2 withObject:[NSNumber numberWithInt:45]];
    [_shapeArray replaceObjectAtIndex:3 withObject:[NSNumber numberWithInt:16]];
    [_shapeArray replaceObjectAtIndex:4 withObject:[NSNumber numberWithInt:60]];
    [_shapeArray replaceObjectAtIndex:5 withObject:[NSNumber numberWithInt:57]];
    [_shapeArray replaceObjectAtIndex:6 withObject:[NSNumber numberWithInt:9]];
    [_shapeArray replaceObjectAtIndex:7 withObject:[NSNumber numberWithInt:28]];

    [_equationArray replaceObjectAtIndex:0 withObject:[NSNumber numberWithInt:28]];
    [_equationArray replaceObjectAtIndex:1 withObject:[NSNumber numberWithInt:45]];
    [_equationArray replaceObjectAtIndex:2 withObject:[NSNumber numberWithInt:23]];
    [_equationArray replaceObjectAtIndex:3 withObject:[NSNumber numberWithInt:9]];
    [_equationArray replaceObjectAtIndex:4 withObject:[NSNumber numberWithInt:60]];
    [_equationArray replaceObjectAtIndex:5 withObject:[NSNumber numberWithInt:52]];
    [_equationArray replaceObjectAtIndex:6 withObject:[NSNumber numberWithInt:57]];
    /****************** ./testing ****************/
    
    
    
}

@end
