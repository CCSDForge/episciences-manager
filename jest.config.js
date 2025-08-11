module.exports = {
  testEnvironment: 'jsdom',
  setupFilesAfterEnv: ['<rootDir>/tests/javascript/setup.js'],
  testMatch: [
    '<rootDir>/tests/javascript/**/*.test.js',
    '<rootDir>/tests/javascript/**/*.spec.js'
  ],
  collectCoverageFrom: [
    'assets/**/*.js',
    '!assets/vendor/**',
    '!assets/node_modules/**',
    '!assets/app.js',
    '!assets/bootstrap.js'
  ],
  transform: {
    '^.+\\.js$': 'babel-jest'
  },
  moduleNameMapper: {
    '\\.(css|less|scss|sass)$': 'identity-obj-proxy'
  },
  coverageThreshold: {
    global: {
      branches: 0,
      functions: 0,
      lines: 0,
      statements: 0
    }
  }
};