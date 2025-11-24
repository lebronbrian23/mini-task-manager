import { shallowMount } from '@vue/test-utils';
import { describe, it, expect } from 'vitest';
import { defineComponent, h } from 'vue';
import AddTaskForm from '../AddTaskForm.vue';

// Comprehensive UI tests for AddTaskForm.vue
// We shallow-mount and stub external components to focus on this SFC's behavior.
describe('AddTaskForm', () => {
  const makeInertiaFormStub = (state?: { errors?: Record<string, string>, processing?: boolean }) =>
    defineComponent({
      name: 'InertiaFormStub',
      props: {
        resetOnSuccess: { type: [Boolean, String], default: false },
      },
      setup(props, { slots }) {
        const providedErrors = state?.errors ?? {};
        const providedProcessing = state?.processing ?? false;
        return () => h(
          'form',
          { 'reset-on-success': String((props as any).resetOnSuccess) },
          slots.default ? slots.default({ errors: providedErrors, processing: providedProcessing }) : null,
        );
      },
    });

  const mountWithStubs = (slotState?: { errors?: Record<string, string>, processing?: boolean }) =>
    shallowMount(AddTaskForm, {
      props: {},
      global: {
        stubs: {
          // Inertia
          Head: {
            name: 'Head',
            props: ['title'],
            template: '<div data-test="head" />',
          },
          Link: true,
          // Layout - keep slot content visible and allow prop inspection
          AppLayout: { name: 'AppLayout', props: ['breadcrumbs'], template: '<div data-test="applayout"><slot /></div>' },
          // UI primitives
          Button: { template: '<button type="submit"><slot /></button>' },
          Input: { template: '<input />' },
            Textarea: { template: '<textarea><slot /></textarea>' },
          Label: { template: '<label><slot /></label>' },
          InputError: {
            props: ['message'],
            template: '<p class="input-error">{{ message }}</p>',
          },
          // Icon - provide a sentinel element we can detect
          LoaderCircle: { template: '<svg data-test="loader" />' },
          // Inertia Form stub with scoped slot data we can control per-test
          Form: makeInertiaFormStub(slotState),
        },
      },
    });


  it('shows the status message when provided', () => {
    const wrapper = shallowMount(AddTaskForm, {
      props: { status: 'Task created successfully' },
      global: {
        stubs: {
          Head: { props: ['title'], template: '<div />' },
          Link: true,
          AppLayout: { template: '<div><slot /></div>' },
          Button: { template: '<button><slot /></button>' },
          Input: { template: '<input />' },
          Textarea: { template: '<textarea><slot /></textarea>' },
          Label: { template: '<label><slot /></label>' },
          InputError: { props: ['message'], template: '<p class="input-error">{{ message }}</p>' },
          LoaderCircle: { template: '<svg data-test="loader" />' },
          Form: makeInertiaFormStub(),
        },
      },
    });
    expect(wrapper.text()).toContain('Task created successfully');
  });

  it('does not render the status container if no status prop is given', () => {
    const wrapper = mountWithStubs();
    expect(wrapper.find('.text-green-600').exists()).toBe(false);
  });

  it('passes breadcrumbs to AppLayout', () => {
    const wrapper = mountWithStubs();
    const layout = wrapper.findComponent({ name: 'AppLayout' });
    expect(layout.exists()).toBe(true);
    const breadcrumbs = (layout as any).props('breadcrumbs');
    expect(Array.isArray(breadcrumbs)).toBe(true);
    expect(breadcrumbs[0].title).toBe('Add task');
  });

  it('renders Name input with correct attributes and label', () => {
    const wrapper = mountWithStubs();
    const label = wrapper.find('label[for="name"]');
    expect(label.exists()).toBe(true);
    expect(label.text()).toBe('Name');

    const input = wrapper.find('input#name');
    expect(input.exists()).toBe(true);
    expect(input.attributes('type')).toBe('text');
    expect(input.attributes('name')).toBe('name');
    expect(input.attributes('placeholder')).toBe('Enter task name');
    expect(input.attributes('required')).toBeDefined();
    expect(input.attributes('tabindex')).toBe('1');
  });

  it('renders Description textarea with correct attributes and label', () => {
    const wrapper = mountWithStubs();
    const label = wrapper.find('label[for="description"]');
    expect(label.exists()).toBe(true);
    expect(label.text()).toBe('Description');

    const textarea = wrapper.find('textarea#description');
    expect(textarea.exists()).toBe(true);
    expect(textarea.attributes('name')).toBe('description');
    expect(textarea.attributes('cols')).toBe('30');
    expect(textarea.attributes('rows')).toBe('10');
    expect(textarea.attributes('tabindex')).toBe('2');
  });

  it('renders the submit button with type submit and text', () => {
    const wrapper = mountWithStubs();
    const button = wrapper.find('button[type="submit"]');
    expect(button.exists()).toBe(true);
    expect(button.text()).toContain('Add task');
  });

  it('displays validation errors from the Form slot', () => {
    const wrapper = mountWithStubs({ errors: { name: 'Name is required', description: 'Description is required' } });
    const errors = wrapper.findAll('.input-error');
    // Our stub renders two errors: one for name and one for description
    expect(errors.map(e => e.text())).toEqual(
      expect.arrayContaining(['Name is required', 'Description is required'])
    );
  });


  it('sets reset-on-success="true" on the Form', () => {
    const wrapper = mountWithStubs();
    const form = wrapper.find('form');
    expect(form.exists()).toBe(true);
    // Our stub stringifies the prop so we can assert the attribute value
    expect(form.attributes('reset-on-success')).toBe('true');
  });
});
