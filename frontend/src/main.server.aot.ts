/**
 * This file should be temporary
 * See https://github.com/angular/angular-cli/pull/5194
 */
import 'zone.js/dist/zone-node';
import 'reflect-metadata';
import 'rxjs/Rx';
import * as express from 'express';
import {platformServer, renderModuleFactory} from '@angular/platform-server';
import {ServerAppModuleNgFactory} from './ngfactory/app/server-app.module.ngfactory';
import {ngExpressEngine} from './sys/ng-express-engine/express-engine';
import {ROUTES} from './routes';
import {enableProdMode} from '@angular/core';

if (process.env.NODE_ENV === 'production') {
    enableProdMode();
}

const app = express();
const port = 8000;
const baseUrl = `http://localhost:${port}`;

app.engine('html', ngExpressEngine({
    aot: true,
    bootstrap: ServerAppModuleNgFactory
}));

app.set('view engine', 'html');
app.set('views', 'src');

app.use('/', express.static('dist', {index: false}));

app.get('/sitemap.xml', (req, res) => {
    res.redirect(301, `${env.backendUrl}/sitemap.xml`);
});

ROUTES.forEach(route => {
    app.get(route, (req, res) => {
        console.time(`GET: ${req.originalUrl}`);
        res.render('../dist/index', {
            req: req,
            res: res
        });
        console.timeEnd(`GET: ${req.originalUrl}`);
    });
});

app.get('*', (req, res) => {
    res.status(404).redirect('/404');
});

app.listen(8000, () => {
    console.log(`Listening at ${baseUrl}`);
});
