import {Component, OnInit} from '@angular/core';
import {MetaTagsService} from '../../../core'
import {NoticesService} from '../../../lib';
import {
    ApiService,
    TitleService,
    ProcessLockerServiceProvider,
    ProcessLockerService,
    NavigatorServiceProvider,
    NavigatorService,
} from '../../../shared';
import {Subscription as Model} from './models';

@Component({
    selector: 'subscription-form',
    templateUrl: 'subscription-form.component.html',
})
export class SubscriptionFormComponent implements OnInit {
    protected model:Model;
    protected navigator:NavigatorService;
    protected processLocker:ProcessLockerService;

    constructor(protected api:ApiService,
                protected title:TitleService,
                protected metaTags:MetaTagsService,
                protected notices:NoticesService,
                navigatorProvider:NavigatorServiceProvider,
                processLockerServiceProvider:ProcessLockerServiceProvider) {
        this.navigator = navigatorProvider.getInstance();
        this.processLocker = processLockerServiceProvider.getInstance();
    }

    ngOnInit():void {
        this.model = new Model;
        this.title.setTitle('Subscription');
        this.metaTags.setTitle(this.title.getPageName());
    }

    subscribe = ():Promise<any> => {
        return this.processLocker
            .lock(() => this.api.post('/subscriptions', this.model))
            .then(this.onSubscribeSuccess);
    };

    onSubscribeSuccess = (data:any):any => {
        this.notices.success('You have been successfully subscribed to the website updates.');
        this.navigator.navigate(['/']);
        return data;
    };

    isProcessing = ():boolean => {
        return this.processLocker.isLocked();
    };
}
