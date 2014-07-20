//
//  Picture.m
//  MathApp
//
//  Created by Xiaoyun Yang on 7/3/14.
//  Copyright (c) 2014 Apportable. All rights reserved.
//

#import "Picture.h"

@implementation Picture

@synthesize backgroundImage = _backgroundImage;
@synthesize redBox = _redBox;
@synthesize blackBox = _blackBox;
@synthesize tileRect = _tileRect;
@synthesize pictureName = _pictureName; //e.g. 3x3
@synthesize isSelected = _isSelected;
@synthesize pictureType = _pictureType;
@synthesize pictureOrigin = _pictureOrigin;
@synthesize picture = _picture;

- (id)initWithData :(PictureType)type :(int)x :(int)y :(NSString*)pictureName :(int)width :(int)height {
    self = [super init];
        
    if (self) {
        // Load card graphics
        self.redBox = [UIImage imageNamed:@"Resources/Misc/SmallBorder_On.png"];
        self.blackBox = [UIImage imageNamed:@"Resources/Misc/SmallBorder_Off.png"];
        self.backgroundImage = [UIImage imageNamed:pictureName];
        
        self.pictureType = type;
        // or for example
        //[self setCardType:type];
            
        self.isSelected = false;
        self.pictureName = pictureName;
            
        // Set card position and z order
        CGPoint point;
        point.x = x;
        point.y = y;
        CGRect rect;
        rect.origin = point;
        self.pictureOrigin = point;
        rect.size.width = width;
        rect.size.height = height;
        self.tileRect = rect;
        
        //self.zOrder = z;
    }
    return self;
}

- (void)drawPicture {
    NSLog(@"drawing %@ at position (%f, %f)\n",self.pictureName, self.pictureOrigin.x, self.pictureOrigin.y);
    if(self.isSelected) {
        NSLog(@"self is selected\n");
        //[self.redBox drawInRect:self.tileRect];
    }else {
        NSLog(@"self is NOT selected\n");
        //[self.blackBox drawInRect:self.tileRect];
    }
    //NSString *bundlePath = [[NSBundle mainBundle] pathForResource:@"NumberShapesAsset" ofType:@"bundle"];
    //NSString *imageName = [[NSBundle bundleWithPath:bundlePath] pathForResource:@"ResumeGame" ofType:@"png"];
    //UIImage *myImage = [[UIImage alloc] initWithContentsOfFile:imageName];
    
    //NSString *imgName = @"Pictures.bundle/NumberShapesAsset/ResumeGame.png";
    //UIImage *myImage = [UIImage imageNamed:imgName];
    
    //UIImageView *imgView = [[UIImageView alloc] initWithImage:myImg];
    
    //self.picture = myImage;
    //[self.view addSubview:imgView];
    //[self.picture drawAtPoint:CGPointMake(10, 10)];
    
    
    //[[UIImage imageWithContentsOfFile:@"test.png"] drawInRect:self.tileRect];
    NSLog(@"success drawing picture!");
    //[self.backgroundImage drawInRect:self.tileRect];
    
}



@end
