//
//  Grid.h
//  MathApp
//
//  Created by Xiaoyun Yang on 7/3/14.
//  Copyright (c) 2014 Apportable. All rights reserved.
//

#import "CCNodeColor.h"
//#import "Picture.h"

@interface Grid : CCNodeColor {
}
-(void)refreshGameboard :(NSMutableArray*)shapeArray :(NSMutableArray*)equationArray;
-(NSMutableArray*)tileIndexToGridPosition :(NSString*)type :(int)tileIndex;
//@property NSMutableArray* _tileArray;
@end
