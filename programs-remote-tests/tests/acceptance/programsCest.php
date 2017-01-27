<?php

class programsCest
{
    public function listPrograms(AcceptanceTester $I)
    {
        $I->wantTo('Verify that programs show up');
        $I->amOnPage('/events/');
        $I->see('Multi Person Lodging');
        $I->see('Price Options Program');
        $I->see('No Price Program');
        $I->see('Flex Date Program');
        $I->see('Closed Program');
        $I->dontSee('Past Program');
        $I->dontSee('Hidden Program');

        $I->wantTo('Verify some details of the exhaustive program');
        $I->see('Exhaustive program'); //name
        $I->see('Book now and get 20% off listed prices'); //early bird
        $I->see('Pellentesque habitant'); // description
        // This part of the description should not be visible
        $I->dontSee('Lorem ipsum dolor sit amet, consectetuer adipiscing elit.');
        $I->see(date('F j', strtotime('+100 days'))); // dates

        $I->wantTo('Verify that the teacher text shows up');
        $I->see('With Yogi Bear Test and Kumare Test');

        $I->wantTo('Verify that the listing blurb shows up');
        $I->see('A shortened version of the program description!');
    }

    public function viewProgramCategoryViaURL(AcceptanceTester $I)
    {
        $I->wantTo('Verify that program category by URL works');
        $I->amOnPage('/events/category/plant-medicine');
        $I->see('Example Program');
        $I->see('Exhaustive program');
        $I->dontSee('Multi Person Lodging');
        $I->dontSee('Price Options Program');
        $I->dontSee('No Price Program');
        $I->dontSee('Flex Date Program');
        $I->dontSee('Closed Program');
    }

    public function singleProgram(AcceptanceTester $I)
    {
        $I->amOnPage('/events/');
        $I->click('Exhaustive program');
        $I->see('Exhaustive Program');
        $I->see('With Yogi Bear Test and Kumare Test');
        $I->see('Register now');
        $I->see('Book now and get 20% off listed prices. Only 69 days left!');
        $I->see('$100.00 – Program Price');
        $I->see('Location: Nelson');
        $I->see('Address: 444 Baker St');
        $I->see('Contact details test');
        $I->see('test@retreat.guru');
        $I->see('1-234-5678');
        $I->see('Custom field title');
        $I->see('Custom field value');
        $I->see('Email us about program');
        $I->see('Mauris placerat eleifend leo. Quisque sit amet est et sapien ullamcorper pharetra');
        $I->see('Header Level 3');
        $I->see('Vestibulum auctor dapibus neque');
        $I->see('Teachers');
        $I->see('Learn more about Yogi Bear Test');
        $I->see('A yogi is a practitioner of yoga');
        $I->see('Learn more about Kumare Test');
//        $I->seeInTitle('Nice SEO title'); // todo: enable once new version is in production
//        $I->seeInSource('Nice SEO description');

        // does not work on CircleCI
//        $I->wantTo('ensure register link works');
//        $I->click('Register now');
//        $I->see('Participant Info');
    }
}
