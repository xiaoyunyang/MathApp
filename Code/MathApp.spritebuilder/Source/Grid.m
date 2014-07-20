//
//  Grid.m
//  MathApp
//
//  Created by Xiaoyun Yang on 7/3/14.
//  Copyright (c) 2014 Apportable. All rights reserved.
//


#import "Grid.h"
#import "Picture.h"
#import "ShapeTile.h"
#import "EquationTile.h"

@implementation Grid {
    CGFloat _tileWidth;
    CGFloat _tileHeight;
    CGFloat _tileMarginVertical;
    CGFloat _tileMarginHorizontal;
    NSMutableArray* _gameboardArray;
    NSMutableArray* _equationArray;
    NSMutableArray* _shapeArray;
    NSNull *_noTile;
}
static const NSInteger GRID_SIZE_ROW = 5;
static const NSInteger GRID_SIZE_COL = 3;

- (void)didLoadFromCCB {
    [self setupGridBackground];
    //[self refreshGameboard :nil :nil];
}

#pragma mark - initialization
//*********** setupGridBackground ****************//
//creates 5 rows and 3 columns of Tiles in Grid. Initialize data structure and draw grid matrix boundaries
- (void)setupGridBackground {
    
    //initialize _gridArray
    _gameboardArray = [NSMutableArray array];
    _noTile = [NSNull null];
    //instantiate empty 5x3 grid matrix
    for (int i = 0; i < GRID_SIZE_ROW; i++) {
		_gameboardArray[i] = [NSMutableArray array];
		for (int j = 0; j < GRID_SIZE_COL; j++) {
			_gameboardArray[i][j] = _noTile;
		}
	}
    
    // load one tile to read the dimensions
    /**************** no idea why this results in exc_bad_access *********/
    /*CCNode *tile = [CCBReader load:@"ShapeTile"];
    _columnWidth = tile.contentSize.width;
    _columnHeight = tile.contentSize.height;
     *********************************************************************/
    
    _tileWidth = self.contentSize.width*0.31;
    _tileHeight = self.contentSize.height*0.18;
    _tileMarginHorizontal = (self.contentSize.width - (GRID_SIZE_COL * _tileWidth)) / (GRID_SIZE_COL+1);
    _tileMarginVertical = (self.contentSize.height - (GRID_SIZE_ROW * _tileHeight)) / (GRID_SIZE_ROW+1);
    
    //NSLog(@"\ncolumnWidth: %f\nnolumnHeight: %f", _columnWidth, _columnHeight);
    //NSLog(@"\ngridWidth: %f\ngridHeight: %f", self.contentSize.width, self.contentSize.height);
    //NSLog(@"\ntileMarginHorizontal: %f\ntileMarginVertical: %f", _tileMarginHorizontal, _tileMarginVertical);
    
    //setting up empty tiles
    float x = _tileMarginHorizontal;
    float y = _tileMarginVertical;
    
    for (int i = 0; i < GRID_SIZE_ROW; i++) {
        //iterate through each row
        x = _tileMarginHorizontal;
        
        for (int j = 0; j < GRID_SIZE_COL; j++) {
            CCNode* tile;
            if((i+j)%2==0) {
                tile = [CCBReader load:@"ShapeTile"];
            }else{
                tile = [CCBReader load:@"EquationTile"];
            }
            tile.position = ccp(x, y);
            [self addChild:tile];
            _gameboardArray[i][j] = tile;
            x+= _tileWidth + _tileMarginHorizontal;
        }
        y += _tileHeight + _tileMarginVertical;

    }

    NSLog(@"after initializing grid: grid matrix (rows, cols) is (%d, %d)\n", [_gameboardArray count], [_gameboardArray[0] count]);
    
}

//************* refreshGameboard ***************//
//to be called by MainScene
//MainScene will provide size 8 shapeArray that matches size 7 equationArray
//this method renders the images for game elements based on the value of the array element, which represents the key that gets mapped to the name of the png file
//location of the tile on the grid associated with each array element is determined by the array index
//example: shapeArray[0] is the key for the shape image which will be displayed at _gameboardArray[0][0]. equationArray[0] is the key for hte equation image which will be displayed at _gameboardArray[0][1]
-(void)refreshGameboard :(NSMutableArray*)shapeArray :(NSMutableArray*)equationArray {
    //NSLog(@"shapeArray: %@", shapeArray);
    //NSLog(@"equationArray: %@", equationArray);
    
    int numShapes = [shapeArray count];
    int numEquations = [equationArray count];
    NSMutableArray* rowCol = [[NSMutableArray alloc] initWithCapacity: 2];
    int row=-1;
    int col=-1;
    
    //refresh shape pictures on gameboard
    for(int i=0; i<numShapes; i++) {
        rowCol = [self tileIndexToGridPosition:@"shape" :i];
        row = [[rowCol objectAtIndex:0] intValue];
        col = [[rowCol objectAtIndex:1] intValue];
        ShapeTile* tile = (ShapeTile*)_gameboardArray[row][col];
        NSNumber* key = [shapeArray objectAtIndex:i];
        //NSLog(@"key is...............%@\n", key);
        [tile changeNodeImage:key];
        
    }
    
    //refresh equation pictures on gameboard
    for(int i=0; i<numEquations; i++) {
        rowCol = [self tileIndexToGridPosition:@"equation" :i];
        row = [[rowCol objectAtIndex:0] intValue];
        col = [[rowCol objectAtIndex:1] intValue];
        EquationTile* tile = (EquationTile*)_gameboardArray[row][col];
        NSNumber* key = [equationArray objectAtIndex:i];
        [tile changeNodeImage:key];
    }
    
    
    //NSLog(@"rowCol is..........%@", rowCol);
    
    

}




//************* spawnImages ***************//
//adds a tile of
//input: pictureName, row, col
//- (Picture*)spawnImage :(NSString*)pictureName :(NSInteger)row :(NSInteger)col {
	//Picture* picture = [Picture alloc];
    //CGPoint position = [self positionForTile:row:col];
    
//}


//************* tileIndexToGridPosition ***************//
//convert from 0...7 (type=equation) and 0...8 (type=shape) to [row col] for tile position in grid
-(NSMutableArray*)tileIndexToGridPosition :(NSString*)type :(int)tileIndex {
    NSMutableArray* rowAndCol = [[NSMutableArray alloc] initWithCapacity:2];
    NSNumber* row;
    NSNumber* col;
    if([type isEqualToString:@"shape"]) {
        switch(tileIndex) {
            case 0:
                row=[NSNumber numberWithInt:0];
                col=[NSNumber numberWithInt:0];
                break;
            case 1:
                row=[NSNumber numberWithInt:0];
                col=[NSNumber numberWithInt:2];
                break;
            case 2:
                row=[NSNumber numberWithInt:1];
                col=[NSNumber numberWithInt:1];
                break;
            case 3:
                row=[NSNumber numberWithInt:2];
                col=[NSNumber numberWithInt:0];
                break;
            case 4:
                row=[NSNumber numberWithInt:2];
                col=[NSNumber numberWithInt:2];
                break;
            case 5:
                row=[NSNumber numberWithInt:3];
                col=[NSNumber numberWithInt:1];
                break;
            case 6:
                row=[NSNumber numberWithInt:4];
                col=[NSNumber numberWithInt:0];
                break;
            case 7:
                row=[NSNumber numberWithInt:4];
                col=[NSNumber numberWithInt:2];
                break;
        }
    }else if([type isEqualToString:@"equation"]) {
        switch(tileIndex) {
            case 0:
                row=[NSNumber numberWithInt:0];
                col=[NSNumber numberWithInt:1];
                break;
            case 1:
                row=[NSNumber numberWithInt:1];
                col=[NSNumber numberWithInt:0];
                break;
            case 2:
                row=[NSNumber numberWithInt:1];
                col=[NSNumber numberWithInt:2];
                break;
            case 3:
                row=[NSNumber numberWithInt:2];
                col=[NSNumber numberWithInt:1];
                break;
            case 4:
                row=[NSNumber numberWithInt:3];
                col=[NSNumber numberWithInt:0];
                break;
            case 5:
                row=[NSNumber numberWithInt:3];
                col=[NSNumber numberWithInt:2];
                break;
            case 6:
                row=[NSNumber numberWithInt:4];
                col=[NSNumber numberWithInt:1];
                break;
        }
    }else {
        NSLog(@"type must be equation or shape\n");
        return nil;
    }
    
    [rowAndCol addObject:row];
    [rowAndCol addObject:col];
    return rowAndCol;
}


//************* positionForTile ***************//
//input: column, row
//output: absolute (x,y) of the tile
- (CGPoint)positionForTile :(NSInteger)row :(NSInteger)col {
	NSInteger x = _tileMarginHorizontal + col * (_tileMarginHorizontal + _tileWidth);
	NSInteger y = _tileMarginVertical + row * (_tileMarginVertical + _tileHeight);
	return CGPointMake(x,y);
}

@end

