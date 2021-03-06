import {Component, OnInit, ViewChild} from '@angular/core';
import {Router, ActivatedRoute} from '@angular/router';
import {MetaTagsService} from '../../../core'
import {GalleryComponent, GalleryImage} from '../../../lib';
import {
    TitleService,
    AuthProviderService,
    NavigatorServiceProvider,
    PagerServiceProvider,
    ProcessLockerServiceProvider,
    ScrollFreezerService,
} from '../../../shared';
import {PhotoDataProviderService} from '../../services';
import {PhotosComponent as AbstractPhotosComponent} from '../abstract';

@Component({
    selector: 'photos-by-tag',
    templateUrl: 'photos-by-tag.component.html',
})
export class PhotosByTagComponent extends AbstractPhotosComponent implements OnInit {
    @ViewChild('galleryComponent') galleryComponent:GalleryComponent;
    protected queryParams:any = {tag: ''};

    constructor(protected authProvider:AuthProviderService,
                protected photoDataProvider:PhotoDataProviderService,
                router:Router,
                route:ActivatedRoute,
                title:TitleService,
                metaTags:MetaTagsService,
                navigatorProvider:NavigatorServiceProvider,
                pagerProvider:PagerServiceProvider,
                processLockerProvider:ProcessLockerServiceProvider,
                scrollFreezer:ScrollFreezerService) {
        super(router, route, title, metaTags, navigatorProvider, pagerProvider, processLockerProvider, scrollFreezer);
    }

    ngOnInit():void {
        super.ngOnInit();
        this.title.setTitle('Search By Tag');
        this.metaTags.setTitle(this.title.getPageName());
    }

    protected initParamsSubscribers() {
        super.initParamsSubscribers();
        this.route.params
            .map((params:any) => params['tag'])
            .filter((tag:any) => tag && tag != this.queryParams['tag'])
            .map((tag:any) => String(tag))
            .subscribe(this.onTagChange.bind(this));
    }

    protected reset():void {
        super.reset();
        this.galleryComponent.reset();
    }

    protected loadImages(page:number, perPage:number, tag:string):Promise<Array<GalleryImage>> {
        return this.processLoadImages(() => this.photoDataProvider.getByTag(page, perPage, tag));
    }

    protected loadMoreImages():Promise<Array<GalleryImage>> {
        return this.loadImages(this.pager.getNextPage(), this.pager.getPerPage(), this.queryParams['tag']);
    }

    protected onTagChange(tag:string):void {
        this.reset();
        this.queryParams['tag'] = tag;
        this.title.setTitle(`Tag #${tag}`);
        this.metaTags.setTitle(this.title.getPageName());
        const perPageOffset = this.queryParams['page'] * this.pager.getPerPage();
        this.loadImages(this.defaults.page, perPageOffset, tag);
    }
}
