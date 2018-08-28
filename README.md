# About hos_sms_dispatcher
This Drupal 8 module dispatches text messages/alert to the sms provider to send to service receipents. The dispatch service (sendMessage) accepts a text message and number to send this message to. A number of http responses will be returned from the SMS provider from which the status of the text message/alert can be determined.

## Inputs

* text message (string)
* receipent number (string)

## Output

* message/alert status code

The production ready code base for this module will reside on the <b>'Master'</b> branch. 

### How to contribute
Github <b>Issues</b> will be used to keep track of tasks, enhancements, and bugs for this module project.
More information on how to use Github issues can be found <a href='https://guides.github.com/features/issues/'>here</a>
<br>A list of issues one can contribute to will be available on this projects issue page. To contribute;
1. pick an issue from the Issue queue
2. check out a new branch and work on it (use feature name or issue number)
3. Submit a Pull request
4. Once changes have been checked and agreed upon, then they will be merged back into the master branch.

### Something to remember
* Every commit message should describe why the code was changed or at a minimum what the change accomplished.
* Use Feature or Issue Branches if you would like to contribute to the project.
