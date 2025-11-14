declare module 'alertify.js' {
  const alertify: AlertifyJs;
  export default alertify;
}

interface AlertifyJs {
  log(message: string): void;
  success(message: string): void;
  error(message: string): void;

  confirm(message: string, okCallback?: () => void, cancelCallback?: () => void): void;
  prompt(message: string, okCallBack:(inputValue: string, e: MouseEvent) => void, cancelCallback?: (e: MouseEvent) => void): void;
}