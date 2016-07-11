<?php
/**
 * PHP code protect
 *
 * @link          https://github.com/Mofsy/pcp-cs
 * @author        Oleg Budrin <ru.mofsy@yandex.ru>
 * @copyright     Copyright (c) 2013-2015, Oleg Budrin (Mofsy)
 */

class Protect
{
    /**
     * Статус лицензии
     *
     * @var bool $status true если активна, false если не активна
     */
    public $status = false;

    /**
     * Ошибки, возникшие при валидации
     *
     * @var bool|string
     */
    public $errors = false;

    /**
     * Ключ активации
     *
     * @var string
     */
    public $activation_key = '';

    /**
     * Срок истечения ключа активации в timestamp, если 0 то лицензия вечная.
     *
     * @var integer
     */
    public $activation_key_expires;

    /**
     * Секретный локальный ключ
     *
     * @var string
     */
    public $secret_key = 'fdfbLhlLgnJDKJklblngkk6krtkghm565678kl78klkUUHtvdfdoghphj';

    /**
     * Полный адрес сервера, для проверки лицензии и выпуска новой.
     *
     * @var string
     */
    public $server = 'http://reg.soc-pro.co/api.php';

    /**
     * Удаленный порт сервера лицензий
     *
     * @var integer
     */
    public $remote_port = 80;

    /**
     * Период ожидания ответа от сервера лицензий
     *
     * @var integer
     */
    public $remote_timeout = 20;

    /**
     * User-agent клиента, который
     * посылается вместе с запросом на сервер лицензий
     *
     * @var string
     */
    public $local_ua = 'PHP code protect';

    /**
     * Маркер использования на локальной системе с Windows без активации
     *
     * @var boolean
     */
    public $use_localhost = false;

    /**
     * Разрешить использовать текущую версию скрипта после истечении срока лицензии
     *
     * NOTE: Если истина, то лицензия будет продолжать работать, даже после истечения срока действия ключа активации.
     * Лицензия будет работать, только на старых релизах скрипта, на новой версии активация уже будет не действительна.
     *
     * @var boolean
     */
    public $use_expires = true;

    /**
     * Маркер режима хранения ключа
     *
     * filesystem - хранить в файле
     * TODO: добавить хранение ключа в базе данных и т.д.
     *
     * @var string
     */
    public $local_key_storage = 'filesystem';

    /**
     * Полный путь до локального файла с временной лицензией
     *
     * @var string
     */
    public $local_key_path = '';

    /**
     * Название файла с временной лицензией
     *
     * @var string
     */
    public $local_key_name = 'license.lic';

    /**
     * Сортировка методов запроса к серверу лицензий.
     *
     * Доступны:
     * s - на сокетах
     * c - на cURL
     * f - на file_get_contents
     *
     * @var string
     */
    public $local_key_transport_order = 'csf';

    /**
     * Период после истечения времени действия локального ключа, после которого лицензия дополнительно дейтвует по
     * времени.
     *
     * @var integer
     */
    public $local_key_delay_period = 7;

    /**
     * Новый локальный ключ
     *
     * @var integer
     */
    public $local_key_last;

    /**
     * Дата релиза скрипта в формате ISO 8601 (YYYY-MM-DD)
     *
     * @var string
     */
    public $release_date = '2014-10-24';

    /**
     * Имя (логин, например на сайте автора) на кого выдана лицензия
     *
     * @var string
     */
    public $user_name = '';

    /**
     * Локализация статусов лицензии и других сообщений
     *
     * @var array
     */
    public $status_messages = array(
        'status_1'                       => 'This activation key is active.',
        'status_2'                       => 'Error: This activation key has expired.',
        'status_3'                       => 'Activation key republished. Awaiting reactivation.',
        'status_4'                       => 'Error: This activation key has been suspended.',
        'localhost'                      => 'This activation key is active (localhost).',
        'pending'                        => 'Error: This activation key is pending review.',
        'download_access_expired'        => 'Error: This version of the software was released after your download access expired. Please downgrade software or contact support for more information.',
        'missing_activation_key'         => 'Error: The activation key variable is empty.',
        'could_not_obtain_local_key'     => 'Error: I could not obtain a new local key.',
        'maximum_delay_period_expired'   => 'Error: The maximum local key delay period has expired.',
        'local_key_tampering'            => 'Error: The local key has been tampered with or is invalid.',
        'local_key_invalid_for_location' => 'Error: The local key is invalid for this location.',
        'missing_license_file'           => 'Error: Please create the following file (and directories if they dont exist already): ',
        'license_file_not_writable'      => 'Error: Please make the following path writable: ',
        'invalid_local_key_storage'      => 'Error: I could not determine the local key storage on clear.',
        'could_not_save_local_key'       => 'Error: I could not save the local key.',
        'activation_key_string_mismatch' => 'Error: The local key is invalid for this activation key.'
    );

    /**
     * Маркер не удачного получения нового локального ключа с сервера
     */
    private $trigger_delay_period;

    /**
     * Конструктор класса
     */
    public function __construct()
    {
		$this->local_key_path = ROOT_PATH.'core'.DS.'media'.DS;
    }

    /**
     * Валидация
     *
     * @return string
     */
    public function validate()
    {
        /*
         * Если локальный компьютер и Windows, а так же разрешено использование
         * на локальном компьютере без активации и локальный ключ не найден.
         */
        if ($this->use_localhost && $this->getIpLocal() && $this->isWindows() && !file_exists("{$this->local_key_path}{$this->local_key_name}")) {
            $this->status = true;

            return $this->errors = $this->status_messages['localhost'];
        }

        /*
         * Если ключ активации пустой, возвращаем ошибку
         */
        if (!$this->activation_key) {
            return $this->errors = $this->status_messages['missing_activation_key'];
        }

        /*
         * Получаем локальный ключ из локального хранилища
         */
        switch ($this->local_key_storage) {
            /*
             * Получаем из файла
             */
            case 'filesystem':
                $local_key = $this->readLocalKey();
                break;

            /*
             * По умолчанию возвращаем ошибку
             */
            default:
                return $this->errors = $this->status_messages['missing_activation_key'];
        }

        /*
         * присваиваем сообщение об ошибке, для случая, если не удастся получить новый локальный ключ с сервера
         */
        $this->trigger_delay_period = $this->status_messages['could_not_obtain_local_key'];

        /*
         * Если не возможно получить новый локальный ключ с сервера, но есть льготный период
         */
        if ($this->errors == $this->trigger_delay_period && $this->local_key_delay_period) {
            /*
             * Получаем льготный период
             */
            $delay = $this->processDelayPeriod($this->local_key_last);

            /*
             * Если льготный период имеется
             */
            if ($delay['write']) {
                /*
                 * Записываем новый ключ с учетом льготного периода
                 */
                if ($this->local_key_storage == 'filesystem') {
                    $this->writeLocalKey($delay['local_key'], "{$this->local_key_path}{$this->local_key_name}");
                }
            }

            /*
             * Если все льготные периоды использованы
             */
            if ($delay['errors']) {
                return $this->errors = $delay['errors'];
            }

            /*
             * Если льготные периоды не использованы
             */
            $this->errors = false;

            return $this;
        }

        /*
         * Проверяем, нет ли ошибок, если есть то возвращаем.
         */
        if ($this->errors) {
            return $this->errors;
        }

        /*
         * Проверяем локальный ключ
         */

        return $this->validateLocalKey($local_key);
    }

    /**
     * Расчитываем максимальное время действия льготного периода
     *
     * @param integer $local_key_expires Время действия локального ключа в UNIX формате
     * @param integer $delay Дополнительный срок действия в днях
     *
     * @return integer
     */
    private function calcMaxDelay($local_key_expires, $delay)
    {
        return ((integer)$local_key_expires + ((integer)$delay * 86400));
    }

    /**
     * Обработка льготного периода для локального ключа
     *
     * @param string $local_key
     *
     * @return string
     */
    private function processDelayPeriod($local_key)
    {
        /*
         * Убираем гадости
         */
        $local_key_src = $this->decodeLocalKey($local_key);
        $parts = $this->splitLocalKey($local_key_src);
        $key_data = unserialize($parts[0]);

        /*
         * Получаем дату истечения локального ключа
         */
        $local_key_expires = (integer)$key_data['local_key_expires'];
        unset($parts, $key_data);

        /*
         * Правила льготного периода
         */
        $write_new_key = false;
        $parts = explode("\n\n", $local_key);
        $local_key = $parts[0];

        foreach ($local_key_delay_period = explode(',', $this->local_key_delay_period) as $key => $delay) {
            // добавляем разделитель
            if (!$key) {
                $local_key .= "\n";
            }

            // считаем льготный период
            if ($this->calcMaxDelay($local_key_expires, $delay) > time()) {
                continue;
            }

            $local_key .= "\n{$delay}";

            $write_new_key = true;
        }

        /*
         * Проверяем максимальный лимит льготного периода
         */
        if (time() > $this->calcMaxDelay($local_key_expires, array_pop($local_key_delay_period))) {
            return array('write' => false, 'local_key' => '', 'errors' => $this->status_messages['maximum_delay_period_expired']);
        }

        return array('write' => $write_new_key, 'local_key' => $local_key, 'errors' => false);
    }

    /**
     * Проверка на принадлежность к льготному периоду
     *
     * @param string $local_key
     * @param integer $local_key_expires
     *
     * @return integer
     */
    private function inDelayPeriod($local_key, $local_key_expires)
    {
        $delay = $this->splitLocalKey($local_key, "\n\n");

        if (!isset($delay[1])) {
            return -1;
        }

        return (integer)($this->calcMaxDelay($local_key_expires, array_pop(explode("\n", $delay[1]))) - time());
    }

    /**
     * Декодируем локальный ключ.
     *
     * @param string $local_key
     *
     * @return string
     */
    private function decodeLocalKey($local_key)
    {
        return base64_decode(str_replace("\n", '', urldecode($local_key)));
    }

    /**
     * Разбиваем локальный ключ на части
     *
     * @param string $local_key
     * @param string $token {protect} or \n\n
     *
     * @return string
     */
    private function splitLocalKey($local_key, $token = '{protect}')
    {
        return explode($token, $local_key);
    }

    /**
     * Проверяем дейтвия ключа по параметрам доступа
     *
     * @param string $key
     * @param array $valid_accesses
     *
     * @return array
     */
    private function validateAccess($key, $valid_accesses)
    {
        return in_array($key, (array)$valid_accesses);
    }

    /**
     * Получаем массив возможных IP адресов
     *
     * @param string $key
     *
     * @return array
     */
    private function wildcardIp($key)
    {
        $octets = explode('.', $key);

        array_pop($octets);
        $ip_range[] = implode('.', $octets) . '.*';

        array_pop($octets);
        $ip_range[] = implode('.', $octets) . '.*';

        array_pop($octets);
        $ip_range[] = implode('.', $octets) . '.*';

        return $ip_range;
    }

    /**
     * Получаем server hostname с учетом wildcard
     *
     * @param string $key
     *
     * @return array
     */
    private function wildcardServerHostname($key)
    {
        $hostname = explode('.', $key);
        unset($hostname[0]);

        $hostname = (!isset($hostname[1])) ? array($key) : $hostname;

        return '*.' . implode('.', $hostname);
    }

    /**
     * Получаем определенный набор деталей доступа из экземпляра
     *
     * @param array $instances
     * @param string $enforce
     *
     * @return array
     */
    private function extractAccessSet($instances, $enforce)
    {
	    $return = array();

        foreach($instances as $key => $instance)
        {
            if ($key !== $enforce)
            {
                continue;
            }
	        else
	        {
		        $return = $instance;
	        }
        }

        return $return;
    }

    /**
     * Валидация локального ключа
     *
     * @param string $local_key
     *
     * @return string
     */
    private function validateLocalKey($local_key)
    {
        /*
         * Преобразовываем локальный ключ в удобную форму
         */
        $local_key_src = $this->decodeLocalKey($local_key);

        /*
         * Разделяем локальный ключ на партии
         */
        $parts = $this->splitLocalKey($local_key_src);

        /*
         * Проверяем на наличие всех частей локального ключа, если нет, то мы не можем проверять дальше
         */
        if (!isset($parts[1])) {
            return $this->errors = $this->status_messages['local_key_tampering'];
        }

        /*
         * Проверяем секретный ключ на подделку. Если не совпадают, то возвратим ошибку.
         */
        if (md5((string)$this->secret_key . (string)$parts[0]) != $parts[1]) {
            return $this->errors = $this->status_messages['local_key_tampering'];
        }

        /*
         * Уничтожаем секретный ключ
         */
        unset($this->secret_key);

        /*
         * Преобразовываем данные локального ключа в удобную форму
         */
        $key_data = unserialize($parts[0]);
        $instance = $key_data['instance'];
        unset($key_data['instance']);
        $enforce = $key_data['enforce'];
        unset($key_data['enforce']);

        /*
         * Присваиваем имя на кого была выдана лицензия свойству класса
         */
        $this->user_name = $key_data['user_name'];

        /*
         * Присваиваем срок окончания лицензии свойству класса
         */
        if ((string)$key_data['activation_key_expires'] === 'never') {
            $this->activation_key_expires = 0;
        } else {
            $this->activation_key_expires = (integer)$key_data['activation_key_expires'];
        }

        /*
         * Проверяем лицензионный ключ на принадлежность к полученному лицензионному ключу.
         */
        if ((string)$key_data['activation_key'] !== (string)$this->activation_key) {
            return $this->errors = $this->status_messages['activation_key_string_mismatch'];
        }
        /*
         * Проверяем статус лицензии, если она не активна и срок не истек, то возвращаем ошибку
         */
        if ((integer)$key_data['status'] === 1) {
            return $this->errors = $this->status_messages['status_2'];
        }
		if((integer)$key_data['status'] === 2) {
            return $this->errors = $this->status_messages['status_4'];
		}

        /*
         * Проверяем срок окончания лицензии
         *
         * NOTE: если срок ключа активации истек и стоит запрет на использование после истечение срока, выдаем ошибку.
         */
        if ($this->use_expires === false && (string)$key_data['activation_key_expires'] !== 'never' && (integer)$key_data['activation_key_expires'] < time()) {
            return $this->errors = $this->status_messages['status_2'];
        }

        /*
         * Проверяем срок истечения локального ключа, если он истек и стоит запрет на использование после истечения
         * срока лицензионного ключа, очищаем ключ и пытаемся получить новый.
         */
        if ($this->use_expires === false && (string)$key_data['local_key_expires'] !== 'never' && (integer)$key_data['local_key_expires'] < time()) {

            /*
             * Если имеется доступный льготный период
             */
            if ($this->inDelayPeriod($local_key, $key_data['local_key_expires']) < 0) {
                /*
                 * Очищаем не действительный локальный ключ
                 */
                $this->clearLocalKey();

                /*
                 * Запускаем получение нового ключа.
                 */

                return $this->validate();
            }
        }

        /*
         * Проверяем дату релиза с датой истечения ключа активации, если разрешено использование после
         * истечения срока лицензионного ключа активации. Дата истечения должна быть меньше даты релиза.
         * Негоже использовать новый скрипт со старой лицензией.
         */
        if ($this->use_expires === true
            && (string)$key_data['activation_key_expires'] !== 'never'
            && (integer)$key_data['activation_key_expires'] < strtotime($this->release_date)) {
            return $this->errors = $this->status_messages['download_access_expired'];
        }

        /*
         * Проверяем срок истечения локального ключа, если он истек и есть разрешение на использование
         * после истечения срока лицензионного ключа.
         */
        if ($this->use_expires === true
            && (string)$key_data['local_key_expires'] !== 'never'
            && (integer)$key_data['local_key_expires'] < time()
            && (integer)$key_data['activation_key_expires'] > (integer)$key_data['local_key_expires'] + 604800) {
            /*
             * Если имеется доступный льготный период
             */
            if ($this->inDelayPeriod($local_key, $key_data['local_key_expires']) < 0) {
                /*
                 * Очищаем не действительный локальный ключ
                 */
                $this->clearLocalKey();

                /*
                 * Запускаем получение нового ключа.
                 */

                return $this->validate();
            }
        }

        /*
         * Проверяем права на запуск для текущего окружения:
         *
         * - Проверяем домен. Домен проверяется сразу на поддомены, на разные зоны.
         * - Проверяем IP адрес сервера.
         * - Проверяем имя сервера.
         */
        $conflicts = array();
        $access_details = $this->accessDetails();

        foreach ((array)$enforce as $key) {
            $valid_accesses = $this->extractAccessSet($instance, $key);

            if(sizeof($valid_accesses) > 0 && !$this->validateAccess($access_details[$key], $valid_accesses)) {
                $conflicts[$key] = true;

                if (in_array($key, array('ip', 'server_ip'))) {
                    foreach ($this->wildcardIp($access_details[$key]) as $ip) {
                        if ($this->validateAccess($ip, $valid_accesses)) {
                            unset($conflicts[$key]);
                            break;
                        }
                    }
                } elseif (in_array($key, array('domain'))) {
                    if(isset($key_data['domain_wildcard'])) {
                        if($key_data['domain_wildcard'] == 1 && preg_match("/" . $valid_accesses[0] . "\z/i", $access_details[$key])){
                            $access_details[$key] = '*.' . $valid_accesses[0];
                        }
                        if($key_data['domain_wildcard'] == 2){
                            $exp_domain = explode('.', $valid_accesses[0]);
                            $exp_domain = $exp_domain[0];
                            if(preg_match("/".$exp_domain."/i", $access_details[$key])){
                                $access_details[$key] = '*.' . $valid_accesses[0] . '.*';
                            }
                        }
                        if($key_data['domain_wildcard'] == 3){
                            $exp_domain = explode('.', $valid_accesses[0]);
                            $exp_domain = $exp_domain[0];
                            if(preg_match("/\A" . $exp_domain . "/i", $access_details[$key])){
                                $access_details[$key] = $valid_accesses[0] . '.*';
                            }
                        }
                    }
                    if ($this->validateAccess($access_details[$key], $valid_accesses)) {
                        unset($conflicts[$key]);
                    }
                } elseif (in_array($key, array('server_hostname'))) {
                    if($this->validateAccess($this->wildcardServerHostname($access_details[$key]), $valid_accesses)) {
                        unset($conflicts[$key]);
                    }
                }
            }
        }

        /*
         * Если конфликты для локального ключа остались, выдаем ошибку.
         * Скрипт не имеет права выполняться в данном расположении по указанной лицензии.
         */
        if (count($conflicts) !== 0) {
            return $this->errors = $this->status_messages['local_key_invalid_for_location'];
        }

        $this->errors = $this->status_messages['status_1'];

        return $this->status = true;
    }

    /**
     * Чтение локального временного лицензионного ключа из файла.
     *
     * @return string
     */
    public function readLocalKey()
    {
        // проверяем на наличие папок, если нет, создаем
        if (!is_dir($this->local_key_path)) {
            mkdir($this->local_key_path, 0755, true);
        }

        // проверяем на существования файла с лицензией
        if (!file_exists($path = "{$this->local_key_path}{$this->local_key_name}")) {
            // пробуем создать пустой файл
            $f = @fopen($path, 'w');
            if (!$f) {
                return $this->errors = $this->status_messages['missing_license_file'] . $path;
            } else {
                fwrite($f, '');
                fclose($f);
            }
        }

        // проверяем на возможность записи файла лицензии
        if (!is_writable($path)) {
            @chmod($path, 0777);
            if (!is_writable($path)) {
                @chmod("$path", 0755);
                if (!is_writable($path)) {
                    return $this->errors = $this->status_messages['license_file_not_writable'] . $path;
                }
            }
        }

        // Проверяем на пустоту локального временного ключа
        if (!$local_key = @file_get_contents($path)) {
            // Получаем новый локальный ключ
            $local_key = $this->getServerLocalKey();

            // Проверяем на наличие ошибок
            if ($this->errors) {
                return $this->errors;
            }

            // записываем новый локальный ключ
            $this->writeLocalKey(urldecode($local_key), $path);
        }

        // возвращаем локальный ключ
        return $this->local_key_last = $local_key;
    }

    /**
     * Очищаем временный локальный ключ
     */
    public function clearLocalKey()
    {
        if ($this->local_key_storage === 'filesystem') {
            $this->writeLocalKey('', "{$this->local_key_path}{$this->local_key_name}");
        } else {
            $this->errors = $this->status_messages['invalid_local_key_storage'];
        }
    }

    /**
     * Записываем локальный ключ в файл
     *
     * @param string $local_key
     * @param string $path
     *
     * @return string|boolean (string при ошибке; boolean true при успехе).
     */
    public function writeLocalKey($local_key, $path)
    {
        $fp = @fopen($path, 'w');
        if (!$fp) {
            return $this->errors = $this->status_messages['could_not_save_local_key'];
        }
        @fwrite($fp, $local_key);
        @fclose($fp);

        return true;
    }

    /**
     * Запрос к API сервера лицензий для получения нового локального ключа
     *
     * @return string|false string local key при успехе; boolean false при ошибке.
     */
    private function getServerLocalKey()
    {
        /*
         * Cобираем строку для POST запроса
         */
        $query_string = 'activation_key=' . urlencode($this->activation_key) . '&';
        $query_string .= http_build_query($this->accessDetails());

        /*
         * Проверяем наличие ошибок при получении деталей запроса ($this->accessDetails)
         */
        if ($this->errors) {
            return false;
        }

        /*
         *  Получаем приоритет методов запроса.
         */
        $priority = $this->local_key_transport_order;

        /*
         * Пробуем получать локальный ключ согласно сорировке методов запроса до успеха
         */
        $result = false;

        while (strlen($priority)) {
            $use = substr($priority, 0, 1);

            // если использовать fsockopen()
            if ($use === 's') {
                if ($result = $this->useFsockopen($this->server, $query_string)) {
                    break;
                }
            }

            // если использовать curl()
            if ($use === 'c') {
                if ($result = $this->useCurl($this->server, $query_string)) {
                    break;
                }
            }

            // если использовать fopen()
            if ($use === 'f') {
                if ($result = $this->useFopen($this->server, $query_string)) {
                    break;
                }
            }

            $priority = substr($priority, 1);
        }

        /*
         * Если не удалось выполнить запрос всеми методами,
         * выдаем ошибку получения локального ключа
         */
        if (!$result) {
            $this->errors = $this->status_messages['could_not_obtain_local_key'];

            return false;
        }

        /*
         * Если результат запроса вернул ошибку ключа
         * То выдаем ошибку + можно заменить Error на ошибку с сервера.
         */
        if (substr($result, 0, 7) === 'Invalid') {
            $this->errors = str_replace('Invalid', 'Error', $result);

            return false;
        }

        /*
         * Если результат запроса вернул ошибку (например сервер недоступен)
         */
        if (substr($result, 0, 5) === 'Error') {
            $this->errors = $result;

            return false;
        }

        return $result;
    }

    /**
     * Собираем массив с информацией окружения
     *
     * @return array
     */
    private function accessDetails()
    {
        $access_details = array();

        if (function_exists('phpinfo')) {
            ob_start();
            phpinfo();
            $phpinfo = ob_get_contents();
            ob_end_clean();

            $list = strip_tags($phpinfo);
            $access_details['domain'] = $this->scrapePhpInfo($list, 'HTTP_HOST');
            $access_details['ip'] = $this->scrapePhpInfo($list, 'SERVER_ADDR');
            $access_details['directory'] = $this->scrapePhpInfo($list, 'SCRIPT_FILENAME');
            $access_details['server_hostname'] = $this->scrapePhpInfo($list, 'System');
            $access_details['server_ip'] = @gethostbyname($access_details['server_hostname']);
        }

        // На всякий случай собираем еще данные
        $access_details['domain'] = ($access_details['domain']) ? $access_details['domain'] : $_SERVER['HTTP_HOST'];
        $access_details['ip'] = ($access_details['ip']) ? $access_details['ip'] : $this->serverAddr();
        $access_details['directory'] = ($access_details['directory']) ? $access_details['directory'] : $this->pathTranslated();
        $access_details['server_hostname'] = ($access_details['domain']) ? $access_details['domain'] : $_SERVER['HTTP_HOST'];
        $access_details['server_hostname'] = ($access_details['server_hostname']) ? $access_details['server_hostname'] : 'Unknown';
        $access_details['server_ip'] = ($access_details['server_ip']) ? $access_details['server_ip'] : @gethostbyaddr($access_details['ip']);
        $access_details['server_ip'] = ($access_details['server_ip']) ? $access_details['server_ip'] : 'Unknown';

        foreach ($access_details as $key => $value) {
            $access_details[$key] = ($access_details[$key]) ? $access_details[$key] : 'Unknown';
        }

        return $access_details;
    }

    /**
     * Получаем путь до директории скрипта
     *
     * @return string|boolean string при успехе; boolean при ошибке
     */
    private function pathTranslated()
    {
        $option = array('PATH_TRANSLATED',
            'ORIG_PATH_TRANSLATED',
            'SCRIPT_FILENAME',
            'DOCUMENT_ROOT',
            'APPL_PHYSICAL_PATH');

        foreach ($option as $key) {
            if (!isset($_SERVER[$key]) || strlen(trim($_SERVER[$key])) <= 0) {
                continue;
            }

            if ($this->isWindows() && strpos($_SERVER[$key], '\\')) {
                return @substr($_SERVER[$key], 0, @strrpos($_SERVER[$key], '\\'));
            }

            return @substr($_SERVER[$key], 0, @strrpos($_SERVER[$key], '/'));
        }

        return false;
    }

    /**
     * Получаем айпи адрес сервера
     *
     * @return string|boolean string при успехе; boolean при ошибке
     */
    private function serverAddr()
    {
        $options = array('SERVER_ADDR', 'LOCAL_ADDR');
        foreach ($options as $key) {
            if (isset($_SERVER[$key])) {
                return $_SERVER[$key];
            }
        }

        return false;
    }

    /**
     * Получаем детали доступа используя phpinfo()
     *
     * @param array $all
     * @param string $target
     *
     * @return string|boolean string при успехе; boolean при ошибке
     */
    private function scrapePhpInfo($all, $target)
    {
        $all = explode($target, $all);
        if (count($all) < 2) {
            return false;
        }
        $all = explode("\n", $all[1]);
        $all = trim($all[0]);

        if ($target === 'System') {
            $all = explode(" ", $all);
            $all = trim($all[(strtolower($all[0]) === 'windows' && strtolower($all[1]) === 'nt') ? 2 : 1]);
        }

        if ($target === 'SCRIPT_FILENAME') {
            $slash = ($this->isWindows() ? '\\' : '/');

            $all = explode($slash, $all);
            array_pop($all);
            $all = implode($slash, $all);
        }

        if (substr($all, 1, 1) === ']') {
            return false;
        }

        return $all;
    }

    /**
     * Отправка запросов на API сервера лицензий с используя fsockopen
     *
     * @param string $url
     * @param string $query_string
     *
     * @return string|boolean string при успехе; boolean при ошибке
     */
    private function useFsockopen($url, $query_string)
    {
        if (!function_exists('fsockopen')) {
            return false;
        }

        $url = parse_url($url);

        $fp = @fsockopen($url['host'], $this->remote_port, $errno, $errstr, $this->remote_timeout);

        if (!$fp) {
            return false;
        }

        $header = "POST {$url['path']} HTTP/1.0\r\n";
        $header .= "Host: {$url['host']}\r\n";
        $header .= "Content-type: application/x-www-form-urlencoded\r\n";
        $header .= "User-Agent: " . $this->local_ua . "\r\n";
        $header .= "Content-length: " . @strlen($query_string) . "\r\n";
        $header .= "Connection: close\r\n\r\n";
        $header .= $query_string;

        $result = false;
        fputs($fp, $header);
        while (!feof($fp)) {
            $result .= fgets($fp, 1024);
        }
        fclose($fp);

        if (strpos($result, '200') === false) {
            return false;
        }

        $result = explode("\r\n\r\n", $result, 2);

        if (!$result[1]) {
            return false;
        }

        return $result[1];
    }

    /**
     * Отправка запросов на API сервера лицензий с используя cURL
     *
     * @param string $url
     * @param string $query_string
     *
     * @return string|boolean string при успехе; boolean при ошибке
     */
    private function useCurl($url, $query_string)
    {
        if (!function_exists('curl_init')) {
            return false;
        }

        $curl = curl_init();

        $header[0] = "Accept: text/xml,application/xml,application/xhtml+xml,";
        $header[0] .= "text/html;q=0.9,text/plain;q=0.8,image/png,*/*;q=0.5";
        $header[] = "Cache-Control: max-age=0";
        $header[] = "Connection: keep-alive";
        $header[] = "Keep-Alive: 300";
        $header[] = "Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7";
        $header[] = "Accept-Language: en-us,en;q=0.5";
        $header[] = "Pragma: ";

        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_USERAGENT, $this->local_ua);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
        curl_setopt($curl, CURLOPT_ENCODING, 'gzip,deflate');
        curl_setopt($curl, CURLOPT_AUTOREFERER, true);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $query_string);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, $this->remote_timeout);
        curl_setopt($curl, CURLOPT_TIMEOUT, $this->remote_timeout);

        $result = curl_exec($curl);
        $info = curl_getinfo($curl);
        curl_close($curl);

        if ((integer)$info['http_code'] != 200) {
            return false;
        }

        return $result;
    }

    /**
     * Отправка запросов на API сервера лицензий с используя fopen оболочки file_get_contents()
     *
     * @param string $url
     * @param string $query_string
     *
     * @return string|boolean string при успехе; boolean при ошибке
     */
    private function useFopen($url, $query_string)
    {
        if (!function_exists('file_get_contents') || !ini_get('allow_url_fopen') || !extension_loaded('openssl')) {
            return false;
        }

        $stream = array('http' => array(
            'method'  => 'POST',
            'header'  => "Content-type: application/x-www-form-urlencoded\r\nUser-Agent: " . $this->local_ua,
            'content' => $query_string
        ));

        $context = null;
        $context = stream_context_create($stream);

        return @file_get_contents($url, false, $context);
    }

    /**
     * Определяем Windows систему
     *
     * @return boolean
     */
    private function isWindows()
    {
        return (strtolower(substr(php_uname(), 0, 7)) === 'windows');
    }

    /**
     * Проверяем на локальность сервера
     *
     * @return bool
     */
    private function getIpLocal()
    {
        $local_ip = '';

        if (function_exists('phpinfo')) {
            ob_start();
            phpinfo();
            $phpinfo = ob_get_contents();
            ob_end_clean();

            $list = strip_tags($phpinfo);
            $local_ip = $this->scrapePhpInfo($list, 'SERVER_ADDR');
        }

        // На всякий случай собираем еще данные
        $local_ip = ($local_ip) ? $local_ip : $this->serverAddr();

        if ($local_ip === '127.0.0.1')
            return true;

        return false;
    }
}