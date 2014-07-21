//
//  Picture.h
//  MathApp
//
//  Created by Xiaoyun Yang on 7/3/14.
//  Copyright (c) 2014 Apportable. All rights reserved.
//

#import <Foundation/Foundation.h>

typedef enum {
    Number,
    Shape
} PictureType;

@interface Picture : NSObject {
    UIImage* _backgroundImage; //equation or shape
    UIImage* _redBox; //for selection set
    UIImage* _blackBox; //for selection set
    CGRect _tileRect;
    NSString* _pictureName; //e.g. 3x3
    BOOL _isSelected;
    PictureType _pictureType;
    CGPoint _pictureOrigin;
    UIImage* _picture;
    
}

-(id)initWithData :(PictureType)type :(int)x :(int)y :(NSString*)pictureName :(int)width :(int)height;
- (void)drawPicture;
//-(void)cancelSelection;

@property (nonatomic, retain) UIImage* backgroundImage;
@property (nonatomic, retain) UIImage* redBox;
@property (nonatomic, retain) UIImage* blackBox;
@property CGRect tileRect;
@property NSString* pictureName;
@property BOOL isSelected;
@property PictureType pictureType;
@property CGPoint pictureOrigin;
@property UIImage* picture;
@end
