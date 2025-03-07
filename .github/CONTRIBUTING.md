# Contributiion Guidelines

First off, thanks for taking the time to contribute! ❤️

All types of contributions are encouraged and valued. See the [Table of Contents](#table-of-contents) for different ways to help and details about how this project handles them. Please make sure to read the relevant section before making your contribution. It will make it a lot easier for us maintainers and smooth out the experience for all involved. The community looks forward to your contributions. 🎉

## "I Have a Question!"

Before you ask a question, it is best to search for existing [Issues](https://github.com/retched/ZC-USPSRestful/issues) that might help you. If you have found a suitable issue and still need clarification, you can write your question about this issue. I would also suggest that you search the Internet for answers first.

If you then still feel the need to ask a question and need clarification, we recommend the following:

- Open an [Issue](https://github.com/retched/ZC-USPSRestful/issues/new).
- Provide as much context as you can about what you're running into.


**Great Bug Reports** tend to have:

- A quick summary and/or background
- Steps to reproduce
  - Be specific! If you have any other modules or non-standard changes to your ZenCart, you should list them too.
- What you expected would happen
- What happens
- Notes (possibly including why you think this might be happening, or stuff you tried that didn't work)
- What is the environment that the script is running from? (What version of PHP? What version of the script? What version of ZenCart?)

The more details available, the easier the problem is to track down and start issuing bug fixes.

I will then take care of the issue as soon as possible.

Additionally, you can use Discord (@retched) in addition to reaching out via this form as well as through the ZenCart support forums..

## Develop with Github

We use GitHub to host code, to track issues and feature requests, as well as accept pull requests. All submissions should be presented through the "GitHub Flow" (as discussed below). If you don't want to go through the hassle of making a GitHub account, you can submit suggestions through the [ZenCart Support Thread](https://www.zen-cart.com/showthread.php?230512-USPS-Shipping-(RESTful)-(USPSr)) for the module.

## [Github Flow](https://docs.github.com/en/get-started/using-github/github-flow) is the preferred process of suggesting changes

Pull requests are the best way to propose changes to the codebase (we use [Github Flow](https://docs.github.com/en/get-started/using-github/github-flow)). We actively welcome your pull requests:

1. Fork the repo and create your branch from `main` (do not use any of the version side branches UNLESS necessary). Do not worry about changing the version number, all changes should be made in the 0.0.0 directory.
2. Make the changes you wish to suggest.
3. Issue that pull request!
   - Be as descriptive as you can in your Pull Request notes. (What's being changed? Why is it being changed? Is it a benefit or boon to the project as a whole? What issue does your code solve?)

## Coding Style

If you're submitting a pull request, PLEASE try to adhere to the PSR-12 standard. Similar to that of the main [Zen Cart Coding Standards](https://docs.zen-cart.com/dev/contributing/coding_standards/).

In summary, your submitted code should be (at a minimum):

- Using four spaces for indentation rather than tabs.
- Functions' opening brace should be on the next line after the declaration. See below for an example:

  ``` php
  function uspsr_someFunctionName($variables_if_needed = '', $some_boolean = TRUE)
  {
    // Code here
  }
  ```

- When inserting comments, try to use the PHPDocumentor coding standard (also known as the [PHP DocBlock](https://docs.phpdoc.org/guide/getting-started/what-is-a-docblock.html) syntax). Otherwise, be as descriptive as you feel you need to be.

## License

By contributing, you agree that your contributions will be licensed under its [GNU-GPL v3 License](https://choosealicense.com/licenses/gpl-3.0/). A copy of this license is found in the LICENSE file of this repository. In short, when you submit code changes, your submissions are understood to be under the same [GNU GPL-v3 License](http://choosealicense.com/licenses/gpl-v3/) that covers the project. Feel free to contact the maintainers if that's a concern.
