import { Component, NO_ERRORS_SCHEMA } from '@angular/core';
import { ComponentFixture, TestBed } from '@angular/core/testing';
import { By } from '@angular/platform-browser';
import { ChangePasswordOverlayComponent } from './change-password-overlay.component';

@Component({
    standalone: false,
    template: `
        <mp-change-password-overlay>
            <span title></span>
            <span action></span>
            <span class="default-slot"></span>
        </mp-change-password-overlay>
    `,
})
class TestHostComponent {}

describe('ChangePasswordOverlayComponent', () => {
    let hostFixture: ComponentFixture<TestHostComponent>;

    beforeEach(() => {
        TestBed.configureTestingModule({
            declarations: [ChangePasswordOverlayComponent, TestHostComponent],
            schemas: [NO_ERRORS_SCHEMA],
        });

        hostFixture = TestBed.createComponent(TestHostComponent);
        hostFixture.detectChanges();
    });

    it('should render <spy-headline> component', () => {
        const headlineComponent = hostFixture.debugElement.query(By.css('spy-headline'));

        expect(headlineComponent).toBeTruthy();
    });

    it('should render `title` slot to the <spy-headline> component', () => {
        const titleSlot = hostFixture.debugElement.query(By.css('spy-headline [title]'));

        expect(titleSlot).toBeTruthy();
    });

    it('should render `action` slot to the <spy-headline> component', () => {
        const actionSlot = hostFixture.debugElement.query(By.css('spy-headline [action]'));

        expect(actionSlot).toBeTruthy();
    });

    it('should render default slot to the `.mp-change-password-overlay__content` element', () => {
        const defaultSlot = hostFixture.debugElement.query(
            By.css('.mp-change-password-overlay__content .default-slot'),
        );

        expect(defaultSlot).toBeTruthy();
    });
});
