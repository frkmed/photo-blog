import {Component, OnInit} from '@angular/core';
import {MetaTagsService} from '../../../core'
import {NoticesService} from '../../../lib';
import {
    AuthService,
    TitleService,
    ProcessLockerServiceProvider,
    ProcessLockerService,
    NavigatorServiceProvider,
    NavigatorService,
} from '../../../shared';
import {SignIn as Model} from './models';

@Component({
    selector: 'signin-form',
    templateUrl: 'signin-form.component.html',
})
export class SignInFormComponent implements OnInit {
    protected model:Model;
    protected navigator:NavigatorService;
    protected processLocker:ProcessLockerService;

    constructor(protected auth:AuthService,
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
        this.title.setTitle('Sing In');
        this.metaTags.setTitle(this.title.getPageName());
    }

    signIn = ():Promise<any> => {
        return this.processLocker
            .lock(() => this.auth.signIn(this.model.email, this.model.password))
            .then(this.onSignInSuccess);
    };

    onSignInSuccess = (user:any):any => {
        this.notices.success('Hello, ' + user.name + '!');
        this.navigator.navigate(['/']);
        return user;
    };

    isProcessing = ():boolean => {
        return this.processLocker.isLocked();
    };
}
