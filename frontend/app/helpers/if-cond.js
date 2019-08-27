import { helper } from '@ember/component/helper';

export function ifCond([v1, operator, v2]) {
  switch (operator) {
    case '==':
        return (v1 == v2) /* ? options.fn(this) : options.inverse(this) */;
    case '===':
        return (v1 === v2);
    case '!=':
        return (v1 != v2);
    case '!==':
        return (v1 !== v2);
    case '<':
        return (v1 < v2);
    case '<=':
        return (v1 <= v2);
    case '>':
        return (v1 > v2);
    case '>=':
        return (v1 >= v2);
    case '&&':
        return (v1 && v2);
    case '||':
        return (v1 || v2);
    default:
        return false;
  }
}

export default helper(ifCond);
