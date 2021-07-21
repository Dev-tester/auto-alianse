<?php

namespace app\models;

use Yii;
use yii\base\Model;

//use yii\web\UploadedFile;

/**
 * This is the model class for table "Uploads".
 *
 * @property int $id
 * @property string $file
 * @property string $created_at
 */
class UploadedFile extends Model{
	/**
	 * @var UploadedFile
	 */
	public $priceFile;
	public $result = [];

    /**
     * {@inheritdoc}
     */
    public function rules(){
        return [
            [['priceFile'], 'file', 'skipOnEmpty' => false, 'extensions' => 'xls,xlsx,doc,docx,pdf', 'maxSize' => 1024*1024*32,'checkExtensionByMimeType' => false]
        ];
    }

	public function upload(){
		if ($this->validate()){
			switch (strtolower($this->priceFile->extension)){
				case "xlsx":
				case "xls":
					$this->proceedExcel($this->priceFile->tempName);
					break;
				case "docx":
				case "doc":
					$this->proceedWord($this->priceFile->tempName);
					break;
				case "pdf":
					$this->proceedPdf($this->priceFile->tempName);
					break;
			}
			return true;
		}
		else{
			echo "not valid file";
			return false;
		}
	}

	private function proceedExcel($fileName){
    	$data = \moonland\phpexcel\Excel::import($fileName, []);
    	// удаляем пустые объекты и колонки
		foreach ($data[0] as $product_info_){
			$tmp_str = '';
			$product_info = [];
			foreach ($product_info_ as $field => $value){
				if ($field) $product_info[$field] = ltrim(trim($value));
				$tmp_str.= $value;
			}
			if (strlen($tmp_str)) $this->result[] = $product_info;
		}
		return true;
	}

	private function proceedWord($fileName){
		return true;
	}

	private function proceedPdf($fileName){
		return true;
	}
}
