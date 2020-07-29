Feature: earc/data-store

  Background:
    Given data is bootstraped
    Given no data is persisted

    Scenario: empty data repository
      Then find data unknownIdentifier should throw an NoDataExceptionInterface
      Then find all data should return an array of size 0
      Then delete unknownIdentifier via delete should throw no Exception
      Then delete unknownIdentifier via batch delete should throw no Exception
      Then create newIdentifier should return an DataInterface

    Scenario: one data created from repository not yet persisted
      Given a data is created with identifier newIdentifier
      Given the newIdentifier data is set with someText
      Then find newIdentifier should return a DataInterface with the same identifier and get returns someText
      Then find all data should return an array of size 1
      Then find all data should contain an DataInterface with identifier newIdentifier and get returns someText
      Then create newIdentifier should throw an DataExistsExceptionInterface
      Then create otherIdentifier should return an DataInterface
      When delete newIdentifier
      Then find all data should return an array of size 1

    Scenario: one data created but not persisted
      Given a data is created with identifier newIdentifier
      Given the newIdentifier data is set with someText
      Given data is bootstraped
      Then find newIdentifier should return a DataInterface with the same identifier and get returns null
      Then find all data should return an array of size 1
      Then find all data should contain an DataInterface with identifier newIdentifier and get returns null
      Then create newIdentifier should throw an DataExistsExceptionInterface
      Then create otherIdentifier should return an DataInterface
      When batch delete newIdentifier and otherIdentifier
      Then find all data should return an array of size 0

    Scenario: one data persisted via update
      Given a data is created with identifier newIdentifier
      Given the newIdentifier data is set with someText
      Given the newIdentifier data is updated
      Given data is bootstraped
      Then find newIdentifier should return a DataInterface with the same identifier and get returns someText
      Then find all data should return an array of size 1
      Then find all data should contain an DataInterface with identifier newIdentifier and get returns someText
      Then create newIdentifier should throw an DataExistsExceptionInterface
      Then create otherIdentifier should return an DataInterface
      When delete otherIdentifier
      Then find all data should return an array of size 1

    Scenario: one data persisted via batch update
      Given a data is created with identifier newIdentifier
      Given the newIdentifier data is set with someText
      Given the newIdentifier data is batch updated
      Given data is bootstraped
      Then find newIdentifier should return a DataInterface with the same identifier and get returns someText
      Then find all data should return an array of size 1
      Then find all data should contain an DataInterface with identifier newIdentifier and get returns someText
      Then create newIdentifier should throw an DataExistsExceptionInterface
      Then create otherIdentifier should return an DataInterface
      When batch delete otherIdentifier and newIdentifier
      Then find all data should return an array of size 0
