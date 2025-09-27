// Add these to the @main.js file:
// import sectionName from '../templates/components/section-name/section-name';
// Alpine.data('sectionName', sectionName);


// Example section.js file:
/* global window, document */
export default (variables) => ({
  variables,
  exampleVar: false,
  init() {
    this.runTestLog();
  },
  runTestLog() {
    console.log(this.variables, this.exampleVar);
  },
});
